<?php

namespace TreeHouse\Queue;

final class QueueEvents
{
    /**
     * Dispatched when a new message is consumed.
     * Listeners receive a ConsumeEvent
     *
     * @see \TreeHouse\Queue\Event\ConsumeEvent
     */
    const CONSUME_MESSAGE = 'queue.consume';

    /**
     * Dispatched after a message is consumed.
     * Listeners receive a ConsumeEvent
     *
     * @see \TreeHouse\Queue\Event\ConsumeEvent
     */
    const CONSUMED_MESSAGE = 'queue.consumed';

    /**
     * Dispatched when consuming a message yields an exception.
     * Listeners receive a ConsumeExceptionEvent
     *
     * @see \TreeHouse\Queue\Event\ConsumeExceptionEvent
     */
    const CONSUME_EXCEPTION = 'queue.consume.exception';

    /**
     * Dispatched when the consumer needs to flush. This typically occurs after a
     * batch completes during the consume command, or when a single message is processed.
     */
    const CONSUME_FLUSH = 'queue.consume.flush';}
