<?php

namespace Nikitakodo\EnqueueWrapper;

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
     * @throws \JsonException
     */
    public function sendMessage(string $queueName, Message $message): void
    {
        /** @var AmqpProducer $producer */
        $producer = $this->context->createProducer();

        if ($message->deliveryDelay) {
            $producer->setDeliveryDelay($message->deliveryDelay);
        }

        $producer->setPriority($message->priority)
            ->send(
                $this->context->createQueue($queueName),
                $this->context->createMessage(json_encode($message, JSON_THROW_ON_ERROR))
            );
    }
}
