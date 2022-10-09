<?php

namespace Nikitakodo\EnqueueWrapper;

use Enqueue\Consumption\Context\Start;
use Enqueue\Consumption\Extension\SignalExtension;

class AsyncSignalExtension extends SignalExtension
{
    /**
     * @throws \Exception
     */
    public function onStart(Start $context): void
    {
        parent::onStart($context);
        pcntl_signal(SIGTERM, [$this, 'handleSignal']);
        pcntl_signal(SIGINT, [$this, 'handleSignal']);
        pcntl_signal(SIGUSR1, [$this, 'handleSignal']);
        pcntl_signal(SIGHUP, [$this, 'handleSignal']);
        pcntl_signal(SIGQUIT, [$this, 'handleSignal']);
    }
}
