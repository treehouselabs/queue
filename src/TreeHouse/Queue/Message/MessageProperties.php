<?php

namespace TreeHouse\Queue\Message;

class MessageProperties implements \ArrayAccess
{
    const CONTENT_TYPE_BASIC      = 'application/octet-stream';
    const CONTENT_TYPE_TEXT_PLAIN = 'text/plain';

    const DELIVERY_MODE_NON_PERSISTENT = 1;
    const DELIVERY_MODE_PERSISTENT     = 2;

    /**
     * @var array
     */
    protected $properties = [];

    /**
     * Constructor.
     *
     * @param array $properties Some default properties will be set if undefined:
     *                          content_type = text/plain and delivery_mode = 2 (persistent)
     */
    public function __construct(array $properties = [])
    {
        foreach ($properties as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $this->properties[$key] = $value;
    }

    /**
     * @param string $key
     *
     * @return boolean
     */
    public function has($key)
    {
        return array_key_exists($key, $this->properties);
    }

    /**
     * @param string $key
     *
     * @return mixed
     *
     * @throws \OutOfBoundsException If the key does not exist
     */
    public function get($key)
    {
        if (!array_key_exists($key, $this->properties)) {
            throw new \OutOfBoundsException(sprintf('Key "%s" does not exist', $key));
        }

        return $this->properties[$key];
    }

    /**
     * @param string $key
     */
    public function remove($key)
    {
        unset($this->properties[$key]);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->properties;
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->get('content_type');
    }

    /**
     * @return integer
     */
    public function getDeliveryMode()
    {
        return $this->get('delivery_mode');
    }
}
