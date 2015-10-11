<?php

namespace TreeHouse\Queue\Amqp;

use TreeHouse\Queue\Exception\ConnectionException;

interface ConnectionInterface
{
    /**
     * Get the configured host.
     *
     * @return string The configured hostname of the broker
     */
    public function getHost();

    /**
     * Get the configured login.
     *
     * @return string The configured login as a string.
     */
    public function getLogin();

    /**
     * Get the configured password.
     *
     * @return string The configured password as a string.
     */
    public function getPassword();

    /**
     * Get the configured port.
     *
     * @return int The configured port as an integer.
     */
    public function getPort();

    /**
     * Get the configured vhost.
     *
     * @return string The configured virtual host as a string.
     */
    public function getVhost();

    /**
     * Set the hostname used to connect to the AMQP broker.
     *
     * @param string $host The hostname of the AMQP broker.
     *
     * @throws ConnectionException If host is longer than 1024 characters.
     *
     * @return bool true on success or false on failure.
     */
    public function setHost($host);

    /**
     * Set the login string used to connect to the AMQP broker.
     *
     * @param string $login The login string used to authenticate with the AMQP broker.
     *
     * @throws ConnectionException If login is longer than 128 characters.
     *
     * @return bool true on success or false on failure.
     */
    public function setLogin($login);

    /**
     * Set the password string used to connect to the AMQP broker.
     *
     * @param string $password The password string used to authenticate with the AMQP broker.
     *
     * @throws ConnectionException If password is longer than 128 characters.
     *
     * @return bool true on success or false on failure.
     */
    public function setPassword($password);

    /**
     * Set the port used to connect to the AMQP broker.
     *
     * @param int $port The port used to connect to the AMQP broker.
     *
     * @throws ConnectionException If port is longer not between 1 and 65535.
     *
     * @return bool true on success or false on failure.
     */
    public function setPort($port);

    /**
     * Sets the virtual host to which to connect on the AMQP broker.
     *
     * @param string $vhost The virtual host to use on the AMQP broker.
     *
     * @throws ConnectionException If host is longer then 32 characters.
     *
     * @return bool true on success or false on failure.
     */
    public function setVhost($vhost);

    /**
     * Establish a connection with the AMQP broker.
     *
     * @throws ConnectionException
     *
     * @return bool true on success
     */
    public function connect();

    /**
     * Closes the connection with the AMQP broker.
     *
     * @return bool true if connection was successfully closed, false otherwise.
     */
    public function close();

    /**
     * Check whether the connection to the AMQP broker is still valid.
     *
     * @return bool true if connected, false otherwise.
     */
    public function isConnected();

    /**
     * Close any open connections and initiate a new one with the AMQP broker.
     *
     * @return bool true on success or false on failure.
     */
    public function reconnect();

    /**
     * When using a decorator, you can use this to get the decorated object.
     *
     * @return mixed
     */
    public function getDelegate();
}
