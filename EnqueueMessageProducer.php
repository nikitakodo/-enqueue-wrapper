<?php

namespace Enqueue\Wrapper;

use Enqueue\AmqpExt\AmqpProducer;
use Enqueue\AmqpTools\DelayStrategy;
use Interop\Queue\Context;
use Interop\Queue\Exception;
use Interop\Queue\Exception\DeliveryDelayNotSupportedException;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\InvalidMessageException;
use Interop\Queue\Exception\PriorityNotSupportedException;

class EnqueueMessageProducer implements MessageProducerInterface
{
    private Context $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @throws Exception
     * @throws DeliveryDelayNotSupportedException
     * @throws PriorityNotSupportedException
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    public function sendMessage(string $queueName, MessageInterface $message): void
    {
        /** @var AmqpProducer $producer */
        $producer = $this->context->createProducer();

        if ($message->getDelayStrategy()) {
            $producer->setDelayStrategy($message->getDelayStrategy());
        }

        if ($message->getDeliveryDelay()) {
            $producer->setDeliveryDelay($message->getDeliveryDelay());
        }

        $producer->setPriority($message->getPriority())
            ->send(
                $this->context->createQueue($queueName),
                $this->context->createMessage(serialize($message))
            );
    }

    public function createMessage(
        string $body,
        int $repeatCount = null,
        int $deliveryDelay = null,
        DelayStrategy $delayStrategy = null,
        int $priority = null
    ): MessageInterface {
        return new EnqueueMessage($body, $repeatCount, $deliveryDelay, $delayStrategy, $priority);
    }
}
