<?php

namespace TreeHouse\Queue\Amqp\Driver\Amqp;

use TreeHouse\Queue\Amqp\ConnectionInterface;
use TreeHouse\Queue\Exception\ConnectionException;

class Connection implements ConnectionInterface
{
    /**
     * @var \AMQPConnection
     */
    protected $delegate;

    /**
     * @param \AMQPConnection $delegate
     */
    public function __construct(\AMQPConnection $delegate)
    {
        $this->delegate = $delegate;
    }

    /**
     * Closes connection on destruct.
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * @inheritdoc
     */
    public function getHost()
    {
        return $this->delegate->getHost();
    }

    /**
     * @inheritdoc
     */
    public function getLogin()
    {
        return $this->delegate->getLogin();
    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return $this->delegate->getPassword();
    }

    /**
     * @inheritdoc
     */
    public function getPort()
    {
        return $this->delegate->getPort();
    }

    /**
     * @inheritdoc
     */
    public function getVhost()
    {
        return $this->delegate->getVhost();
    }

    /**
     * @inheritdoc
     */
    public function setHost($host)
    {
        try {
            return $this->delegate->setHost($host);
        } catch (\AMQPConnectionException $e) {
            throw new ConnectionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function setLogin($login)
    {
        try {
            return $this->delegate->setLogin($login);
        } catch (\AMQPConnectionException $e) {
            throw new ConnectionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function setPassword($password)
    {
        try {
            return $this->delegate->setPassword($password);
        } catch (\AMQPConnectionException $e) {
            throw new ConnectionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function setPort($port)
    {
        try {
            return $this->delegate->setPort($port);
        } catch (\AMQPConnectionException $e) {
            throw new ConnectionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function setVhost($vhost)
    {
        try {
            return $this->delegate->setVhost($vhost);
        } catch (\AMQPConnectionException $e) {
            throw new ConnectionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function connect()
    {
        try {
            return $this->delegate->connect();
        } catch (\AMQPConnectionException $e) {
            throw new ConnectionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        return $this->delegate->disconnect();
    }

    /**
     * @inheritdoc
     */
    public function isConnected()
    {
        return $this->delegate->isConnected();
    }

    /**
     * @inheritdoc
     */
    public function reconnect()
    {
        return $this->delegate->reconnect();
    }

    /**
     * @inheritdoc
     *
     * @return \AMQPConnection
     */
    public function getDelegate()
    {
        return $this->delegate;
    }
}
