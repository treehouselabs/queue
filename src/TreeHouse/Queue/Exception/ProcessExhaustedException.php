<?php

namespace TreeHouse\Queue\Exception;

/**
 * Exception indicating a message could not be processed
 * by the consumer, even after retrying.
 */
class ProcessExhaustedException extends ConsumerException
{
}
