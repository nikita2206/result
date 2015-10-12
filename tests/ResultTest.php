<?php

namespace Result\Tests;

use Result\Err;
use Result\Ok;
use Result\Result;

class ResultTest extends \PHPUnit_Framework_TestCase
{
    public function testOk()
    {
        $this->assertInstanceOf(Ok::class, Result::ok());

        $this->assertSame("asd", Result::ok("asd")->unwrap());

        $this->assertFalse(Result::ok()->isErr());
    }

    public function testOkThrowsExceptionOnGetErr()
    {
        $this->setExpectedException(\RuntimeException::class, "Can't get error of Ok type");

        Result::ok()->getErr();
    }

    public function testErr()
    {
        $this->assertInstanceOf(Err::class, Result::err("err"));

        $this->assertSame("asd", Result::err("asd")->getErr());

        $this->assertTrue(Result::err("asd")->isErr());
    }

    public function testErrThrowsExceptionOnUnwrap()
    {
        $this->setExpectedException(\RuntimeException::class, "Can't unwrap error");

        Result::err("asd")->unwrap();
    }

    public function testErrOnNull()
    {
        $err = Result::errOnNull(null, "err");
        $this->assertInstanceOf(Err::class, $err);
        $this->assertSame("err", $err->getErr());

        $ok = Result::errOnNull("asd", "er");
        $this->assertInstanceOf(Ok::class, $ok);
        $this->assertSame("asd", $ok->unwrap());
    }

    public function testReduceBreaksEarlyOnErr()
    {
        $calls = 0;
        $fn = function () use (&$calls) {
            yield Result::ok(++$calls);
            yield Result::err(++$calls);
            yield Result::ok(++$calls);
        };

        $result = Result::reduce($fn());

        $this->assertSame(2, $calls);
        $this->assertInstanceOf(Err::class, $result);
    }

    public function testReduceYieldsValuesBack()
    {
        $fn = function () {
            $v = (yield Result::ok("asd"));
            $this->assertSame("asd", $v);

            $v = (yield Result::ok(123));
            $this->assertSame(123, $v);
        };

        Result::reduce($fn());
    }

    public function testReduceReturnsExactErr()
    {
        $fn = function () {
            yield Result::ok("asd");
            yield Result::err("foo");
        };

        $res = Result::reduce($fn());

        $this->assertInstanceOf(Err::class, $res);
        $this->assertSame("foo", $res->getErr());
    }

    public function testReduceReturnsLastOk()
    {
        $fn = function () {
            yield Result::ok("asd");
            yield Result::ok("foo");
            yield Result::ok("bar");
        };

        $res = Result::reduce($fn());

        $this->assertInstanceOf(Ok::class, $res);
        $this->assertSame("bar", $res->unwrap());
    }

    public function testRemapErr()
    {
        $ok = Result::ok("asd");
        $this->assertSame($ok, $ok->remapErr(function ($er) { return "err"; }));

        $err = Result::err("fooerr");
        $mapped = $err->remapErr(function ($er) { return ["bar"]; });
        $this->assertSame("fooerr", $err->getErr());
        $this->assertSame(["bar"], $mapped->getErr());
    }
}
