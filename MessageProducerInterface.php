<?php

namespace Enqueue\Wrapper;

use Enqueue\AmqpTools\DelayStrategy;

interface MessageProducerInterface
{
    public function sendMessage(string $queueName, MessageInterface $message): void;

    public function createMessage(
        string $body,
        ?int $repeatCount,
        ?int $deliveryDelay,
        ?DelayStrategy $delayStrategy,
        ?int $priority
    ): MessageInterface;
}
