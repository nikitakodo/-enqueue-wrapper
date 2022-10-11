<?php

namespace Nikitakodo\EnqueueWrapper;

use Enqueue\AmqpTools\DelayStrategy;
use Interop\Amqp\AmqpContext;
use Interop\Amqp\AmqpDestination;
use Interop\Amqp\AmqpMessage;
use Interop\Amqp\AmqpQueue;
use Interop\Amqp\AmqpTopic;
use Interop\Queue\Exception;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\InvalidMessageException;

class QueueDelayStrategy implements DelayStrategy
{
    private string $prefix;
    private int $expireTimeMs;

    public function __construct(string $prefix = 'enqueue', int $expireTimeMs = 24 * 60 * 60 * 1000)
    {
        $this->prefix = $prefix;
        $this->expireTimeMs = $expireTimeMs;
    }

    /**
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     * @throws \Exception
     * @throws Exception
     */
    public function delayMessage(AmqpContext $context, AmqpDestination $dest, AmqpMessage $message, int $delay): void
    {
        $properties = $message->getProperties();

        // The x-death header must be removed because of the bug in RabbitMQ.
        // It was reported that the bug is fixed since 3.5.4, but I tried with 3.6.1 and the bug still there.
        // https://github.com/rabbitmq/rabbitmq-server/issues/216
        unset($properties['x-death']);

        $delayMessage = $context->createMessage($message->getBody(), $properties, $message->getHeaders());
        $delayMessage->setRoutingKey($message->getRoutingKey());

        $expireAt = (new \DateTime())
            ->add((new \DateInterval($this->expireTimeMs / 1000 . ' s')))
            ->format('Y_m_d_h_i_s');

        if ($dest instanceof AmqpTopic) {
            $routingKey = $message->getRoutingKey() ? '.' . $message->getRoutingKey() : '';
            $name = sprintf(
                '%s.%s%s.%s.x.delay.expire_at.%s',
                $this->prefix,
                $dest->getTopicName(),
                $routingKey,
                $delay,
                $expireAt
            );
            $delayQueue = $context->createQueue($name);
            $delayQueue->addFlag(AmqpDestination::FLAG_DURABLE);
            $delayQueue->setArgument('x-expires', $this->expireTimeMs);
            $delayQueue->setArgument('x-message-ttl', $delay);
            $delayQueue->setArgument('x-dead-letter-exchange', $dest->getTopicName());
            $delayQueue->setArgument('x-dead-letter-routing-key', (string)$delayMessage->getRoutingKey());
        } elseif ($dest instanceof AmqpQueue) {
            $name = sprintf('%s.%s.%s.delayed.expire_at.%s', $this->prefix, $dest->getQueueName(), $delay, $expireAt);
            $delayQueue = $context->createQueue($name);
            $delayQueue->addFlag(AmqpDestination::FLAG_DURABLE);
            $delayQueue->setArgument('x-expires', $this->expireTimeMs);
            $delayQueue->setArgument('x-message-ttl', $delay);
            $delayQueue->setArgument('x-dead-letter-exchange', '');
            $delayQueue->setArgument('x-dead-letter-routing-key', $dest->getQueueName());
        } else {
            throw new InvalidDestinationException(sprintf(
                'The destination must be an instance of %s but got %s.',
                AmqpTopic::class . '|' . AmqpQueue::class,
                \get_class($dest)
            ));
        }

        $context->declareQueue($delayQueue);

        $context->createProducer()->send($delayQueue, $delayMessage);
    }
}
