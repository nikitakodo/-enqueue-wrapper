<?php

namespace Enqueue\Wrapper;

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
        attach_signal(SIGTERM, [$this, 'handleSignal']);
        attach_signal(SIGINT, [$this, 'handleSignal']);
        attach_signal(SIGUSR1, [$this, 'handleSignal']);
        attach_signal(SIGHUP, [$this, 'handleSignal']);
        attach_signal(SIGQUIT, [$this, 'handleSignal']);
    }
}
