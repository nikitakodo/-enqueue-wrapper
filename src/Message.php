<?php

namespace Nikitakodo\EnqueueWrapper;

use Enqueue\AmqpTools\DelayStrategy;

class Message implements \JsonSerializable
{
    public string $body;
    public ?int $repeatCount;
    public ?int $deliveryDelay;
    public ?int $priority;

    public function __construct(string $body, int $repeatCount = null, int $deliveryDelay = null, int $priority = null)
    {
        $this->body = $body;
        $this->repeatCount = $repeatCount;
        $this->deliveryDelay = $deliveryDelay;
        $this->priority = $priority;
    }

    public function jsonSerialize(): array
    {
        return [
            'meta' => [
                'repeat_count' => $this->repeatCount,
                'delivery_delay' => $this->deliveryDelay,
                'priority' => $this->priority,
            ],
            'message' => $this->body,
        ];
    }
}
