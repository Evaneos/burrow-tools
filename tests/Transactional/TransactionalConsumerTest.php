<?php

namespace Burrow\tests\Transactional;

use Burrow\Exception\ConsumerException;
use Burrow\QueueConsumer;
use Burrow\Transactional\TransactionalConsumer;
use RemiSan\TransactionManager\Exception\BeginException;
use RemiSan\TransactionManager\Transactional;

class TransactionalConsumerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QueueConsumer
     */
    private $consumer;

    /**
     * @var Transactional
     */
    private $transactionManager;

    protected function tearDown()
    {
        \Mockery::close();
    }

    protected function setUp()
    {
        $this->consumer = \Mockery::mock(QueueConsumer::class);
        $this->transactionManager = \Mockery::mock(Transactional::class);
    }

    /**
     * @test
     */
    public function it_should_throw_ConsumerExeption_if_beginTransaction_fails()
    {
        $this->setExpectedException(ConsumerException::class);

        $this->transactionManager
            ->shouldReceive('beginTransaction')
            ->andThrow(BeginException::class);

        $consumer = new TransactionalConsumer($this->consumer, $this->transactionManager);

        $consumer->consume('test');
    }

    /**
     * @test
     */
    public function it_should_commit_if_consumer_consumes_normally()
    {
        $this->transactionManager->shouldReceive('beginTransaction');
        $this->transactionManager->shouldReceive('commit');

        $this->consumer->shouldReceive('consume');

        $consumer = new TransactionalConsumer($this->consumer, $this->transactionManager);

        $consumer->consume('test');
    }

    /**
     * @test
     */
    public function it_should_rollback_if_consumer_throws_exception()
    {
        $this->setExpectedException(\Exception::class);

        $this->transactionManager->shouldReceive('beginTransaction');
        $this->transactionManager->shouldReceive('commit')->never();
        $this->transactionManager->shouldReceive('rollback');

        $this->consumer->shouldReceive('consume')->andThrow(\Exception::class);

        $consumer = new TransactionalConsumer($this->consumer, $this->transactionManager);

        $consumer->consume('test');
    }
}
