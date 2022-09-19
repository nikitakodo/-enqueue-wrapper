<?php

namespace Nikitakodo\EnqueueWrapper;

use Enqueue\AmqpTools\DelayStrategy;

class EnqueueMessage implements MessageInterface
{
    public string $body;
    public ?int $repeatCount;
    public ?int $deliveryDelay;
    public ?DelayStrategy $delayStrategy;
    public ?int $priority;

    public function __construct(
        string $body,
        int $repeatCount = null,
        int $deliveryDelay = null,
        DelayStrategy $delayStrategy = null,
        int $priority = null
    ) {
        $this->body = $body;
        $this->repeatCount = $repeatCount;
        $this->deliveryDelay = $deliveryDelay;
        $this->delayStrategy = $delayStrategy;
        $this->priority = $priority;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): MessageInterface
    {
        $this->body = $body;
        return $this;
    }

    public function getRepeatCount(): ?int
    {
        return $this->repeatCount;
    }

    public function setRepeatCount(int $count = 0): MessageInterface
    {
        $this->repeatCount = $count;
        return $this;
    }

    public function getDeliveryDelay(): ?int
    {
        return $this->deliveryDelay;
    }

    public function setDeliveryDelay(int $delay = null): MessageInterface
    {
        $this->deliveryDelay = $delay;
        return $this;
    }

    public function getDelayStrategy(): ?DelayStrategy
    {
        return $this->delayStrategy;
    }

    public function setDelayStrategy(?DelayStrategy $delayStrategy): MessageInterface
    {
        $this->delayStrategy = $delayStrategy;
        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(int $priority = null): MessageInterface
    {
        $this->priority = $priority;
        return $this;
    }
}
