<?php

namespace Result;

/**
 * Result data type used for expected failures
 * when function needs to return either success or failure
 * and provide with additional information about
 * the nature of the failure.
 */
abstract class Result
{
    /**
     * @param mixed $data
     *
     * @return Ok
     */
    public static function ok($data = null)
    {
        return new Ok($data);
    }

    /**
     * @param mixed $error
     *
     * @return Err
     */
    public static function err($error)
    {
        return new Err($error);
    }

    /**
     * @param mixed $data
     * @param mixed $err
     * @return Result
     */
    public static function errOnNull($data, $err)
    {
        return $data === null ? new Err($err) : new Ok($data);
    }

    /**
     * When you need to return Result from your function, and it also depends on another
     * functions returning Results, you can make it a generator function and yield
     * values from dependant functions, this pattern makes code less bloated with
     * statements like this:
     * $res = something();
     * if ($res instanceof Ok) {
     *     $something = $res->unwrap();
     * } else {
     *     return $res;
     * }
     *
     * Instead you can write:
     * $something = (yield something());
     *
     * @see /example.php
     *
     * @param \Generator $resultsGenerator Generator that produces Result instances
     * @return Result
     */
    public static function reduce(\Generator $resultsGenerator)
    {
        /** @var Result $result */
        $result = $resultsGenerator->current();

        while ($resultsGenerator->valid()) {
            if ($result instanceof Err) {
                return $result;
            }

            $tmpResult = $resultsGenerator->send($result->unwrap());
            if ($resultsGenerator->valid()) {
                $result = $tmpResult;
            }
        }

        return $result;
    }

    /**
     * @param callable $mapper
     * @return Result
     */
    public function remapErr(callable $mapper)
    {
        if ($this instanceof Ok) {
            return $this;
        } else {
            return Result::err($mapper($this->getErr()));
        }
    }

    /**
     * @return bool
     */
    public abstract function isErr();

    /**
     * @return mixed
     */
    public abstract function unwrap();

    /**
     * @return mixed
     */
    public abstract function getErr();
}
