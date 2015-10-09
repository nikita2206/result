<?php

namespace Result;

class Ok extends Result
{

    /** @var mixed Success response if present */
    protected $data;

    /**
     * @param mixed $data
     */
    public function __construct($data = null)
    {
        $this->data = $data;
    }

    /**
     * @inheritdoc
     */
    public function unwrap()
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function isErr()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getErr()
    {
        throw new \RuntimeException("Can't get error of Ok type");
    }
}
