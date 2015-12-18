<?php

namespace TreeHouse\Queue\Tests\Mock;

class ObjectMock implements \JsonSerializable
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return [$this->id];
    }
}
