<?php

namespace Nikitakodo\EnqueueWrapper;

interface ProcessorConfigInterface
{
    public function getQueueName(): string;
}
