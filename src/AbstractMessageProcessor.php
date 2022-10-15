<?php

namespace Nikitakodo\EnqueueWrapper;

use Enqueue\AmqpExt\AmqpProducer;
use Enqueue\AmqpTools\DelayStrategy;
use Interop\Amqp\AmqpMessage;
use Interop\Amqp\Impl\AmqpQueue;
use Interop\Queue\Context;
use Interop\Queue\Exception;
use Interop\Queue\Exception\DeliveryDelayNotSupportedException;
use Interop\Queue\Message;
use Nikitakodo\EnqueueWrapper\Message as WrapperMessage;

abstract class AbstractMessageProcessor implements MessageProcessorInterface
{
    public const MAX_REQUEUE_COUNT = 10;

    /**
     * @param Message $message
     * @param Context $context
     * @return string
     * @throws \Exception
     */
    abstract public function process(Message $message, Context $context): string;

    /**
     * @return string
     */
    abstract public function getQueueName(): string;

    /**
     * Requeue queue message.
     * Requires message which need to requeue, queue context and time delay in milliseconds
     * @throws DeliveryDelayNotSupportedException
     * @throws Exception\Exception
     * @throws \Exception
     */
    public function requeueMessage(Context $context, WrapperMessage $message, DelayStrategy $delayStrategy): void
    {
        if ((int)$message->repeatCount >= self::MAX_REQUEUE_COUNT) {
            return;
        }
        $message->repeatCount++;
        /** @var AmqpMessage $delayedMessage */
        $delayedMessage = $context->createMessage(serialize($message));
        /** @var AmqpProducer $producer */
        $producer = $context->createProducer();
        /** @var AmqpQueue $queue */
        $queue = $context->createQueue($this->getQueueName());
        $producer->setDelayStrategy($delayStrategy);
        $producer->setDeliveryDelay($message->deliveryDelay);
        $producer->send($queue, $delayedMessage);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return static::class;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function done(): string
    {
        gc_collect_cycles();

        return self::ACK;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function setBack(): string
    {
        return self::REQUEUE;
    }
}
