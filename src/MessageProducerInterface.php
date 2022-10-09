<?php

namespace Nikitakodo\EnqueueWrapper;

use Enqueue\AmqpTools\DelayStrategy;

interface MessageProducerInterface
{
    public function sendMessage(string $queueName, Message $message): void;
}
