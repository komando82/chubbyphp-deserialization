<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization;

use Chubbyphp\Deserialization\DeserializerRuntimeException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\DeserializerRuntimeException
 */
class DeserializerRuntimeExceptionTest extends TestCase
{
    public function testCreateInvalidType()
    {
        $exception = DeserializerRuntimeException::createInvalidType('path1', 'null', 'array');

        self::assertSame('There is an invalid type "null", needed "array" at path: path1', $exception->getMessage());
    }

    public function testCreateNotParsable()
    {
        $exception = DeserializerRuntimeException::createNotParsable('application/json');

        self::assertSame('Data is not parsable with content-type: application/json', $exception->getMessage());
    }

    public function testCreateNotAllowedAddtionalFields()
    {
        $exception = DeserializerRuntimeException::createNotAllowedAddtionalFields(['path1', 'path2']);

        self::assertSame('There are additional field(s) at paths: path1, path2', $exception->getMessage());
    }
}
