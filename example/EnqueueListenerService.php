<?php

use Enqueue\AmqpExt\AmqpContext;
use Enqueue\Consumption\QueueConsumer;
use Nikitakodo\EnqueueWrapper\EnqueueMessageProducer;
use Interop\Amqp\AmqpDestination;
use Interop\Amqp\AmqpQueue;

class EnqueueListenerService
{
    /**
     * @var array<string, AbstractMessageProcessor>
     */
    private array $enqueueProcessors = [];

    /**
     * @var array<string, AbstractMessageProcessor>
     */
    private array $boundProcessors = [];

    private ?EnqueueMessageProducer $defaultMessageProducer = null;

    private QueueConsumer $queueConsumer;

    public function __construct(QueueConsumer $queueConsumer)
    {
        $this->queueConsumer = $queueConsumer;
    }

    /**
     * @param array<int, string> $processors
     */
    public function registerProcessors(array $processors): void
    {
        foreach ($processors as $processorClass) {
            $this->registerProcessor($processorClass);
        }
    }

    public function registerProcessor(string $processorClass): void
    {
        /** @var AbstractMessageProcessor $processor */
        $processor = new $processorClass();
        $this->enqueueProcessors[$processor->getQueueName()] = $processor;
    }

    /**
     * @throws \Throwable
     */
    public function bindProcessor(string $queueName): void
    {
        $processor = $this->enqueueProcessors[$queueName];
        $this->declareQueue($processor->getQueueName());
        $this->queueConsumer->bind($processor->getQueueName(), $processor);
        $this->boundProcessors[$queueName] = $processor;
    }

    /**
     * Start handling queues (blocking process)
     * @throws \Throwable
     */
    public function startConsume(): void
    {
        if (empty($this->getBoundProcessors())) {
            throw new \RuntimeException('There are no bounded processors to consume');
        }

        $this->queueConsumer->consume();
    }

    /**
     * @return array<string, AbstractMessageProcessor>
     */
    public function getBoundProcessors(): array
    {
        return $this->boundProcessors;
    }

    public function getQueueConsumer(): QueueConsumer
    {
        return $this->queueConsumer;
    }

    public function getDefaultMessageProducer(): EnqueueMessageProducer
    {
        if (!$this->defaultMessageProducer) {
            $this->defaultMessageProducer = new EnqueueMessageProducer($this->queueConsumer->getContext());
        }

        return $this->defaultMessageProducer;
    }

    private function declareQueue(string $queueName): void
    {
        /** @var AmqpContext $context */
        $context = $this->queueConsumer->getContext();
        /** @var AmqpQueue $queue */
        $queue = $context->createQueue($queueName);
        $queue->addFlag(AmqpDestination::FLAG_DURABLE);
        $context->declareQueue($queue);
    }
}
