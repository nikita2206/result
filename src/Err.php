<?php

namespace Result;

class Err extends Result
{

    /** @var mixed Error data */
    protected $error;

    /**
     * @param mixed $error
     */
    public function __construct($error)
    {
        $this->error = $error;
    }

    /**
     * @inheritdoc
     */
    public function getErr()
    {
        return $this->error;
    }

    /**
     * @inheritdoc
     */
    public function isErr()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function unwrap()
    {
        throw new \RuntimeException("Can't unwrap error");
    }
}
