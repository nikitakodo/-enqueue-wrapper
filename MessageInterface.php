<?php

namespace Nikitakodo\EnqueueWrapper;

use Enqueue\AmqpTools\DelayStrategy;

interface MessageInterface
{
    public function getBody(): string;

    public function setBody(string $body): MessageInterface;

    public function getPriority(): ?int;

    public function setPriority(int $priority = null): MessageInterface;

    public function getDeliveryDelay(): ?int;

    public function setDeliveryDelay(int $delay = null): MessageInterface;

    public function getDelayStrategy(): ?DelayStrategy;

    public function setDelayStrategy(DelayStrategy $delayStrategy): MessageInterface;

    public function getRepeatCount(): ?int;

    public function setRepeatCount(int $count = 0): MessageInterface;
}
