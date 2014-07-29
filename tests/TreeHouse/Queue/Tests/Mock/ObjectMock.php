<?php

namespace TreeHouse\Queue\Tests\Mock;

class ObjectMock implements \JsonSerializable
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @param integer $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return integer
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
