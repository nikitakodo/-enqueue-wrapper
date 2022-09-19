<?php

use Enqueue\AmqpExt\AmqpConnectionFactory;
use Enqueue\Consumption\ChainExtension;
use Enqueue\Consumption\QueueConsumer;
use Nikitakodo\EnqueueWrapper\AsyncSignalExtension;

$factory = new AmqpConnectionFactory([
    'host' => 'amqp.host',
    'port' => 'amqp.port',
    'vhost' =>'amqp.vhost',
    'user' => 'amqp.login',
    'pass' => 'amqp.password',
    'persisted' => false,
]);
$context = $factory->createContext();

$consumer = new QueueConsumer(
    $context,
    new ChainExtension([
        new AsyncSignalExtension()
    ])
);
//list of processors class names extended from AbstractMessageProcessor
$processors = [];

$enqueueListenerService = new EnqueueListenerService(
    $consumer
);
$enqueueListenerService->registerProcessors($processors);
