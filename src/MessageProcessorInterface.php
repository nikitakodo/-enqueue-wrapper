<?php

namespace Nikitakodo\EnqueueWrapper;

use Enqueue\AmqpTools\DelayStrategy;
use Interop\Queue\Context;
use Interop\Queue\Processor;

interface MessageProcessorInterface extends Processor
{
    public static function getQueueName(): string;

    public function requeueMessage(Context $context, Message $message, DelayStrategy $delayStrategy);
}
