<?php

namespace Result;

class ErrMapper
{
    /** @var callable */
    protected $callback;

    protected $result;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param Err $err
     * @return Err
     */
    public function map(Err $err)
    {
        $callback = $this->callback;
        return Result::err($callback($err));
    }
}
