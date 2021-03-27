<?php

use ATCM\Core\Helpers\StringHelper;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class StringHelperTest extends TestCase
{
    public function testToPascalCase()
    {
        $input1 = "One Two Three";
        assertEquals("OneTwoThree", StringHelper::toPascalCase($input1));

        $input2 = "One_Two_Three";
        assertEquals("OneTwoThree", StringHelper::toPascalCase($input2, "_"));

        $input3 = "one two three";
        assertEquals("OneTwoThree", StringHelper::toPascalCase($input3));
    }

    public function testToCamelCase()
    {
        $input1 = "One Two Three";
        assertEquals("oneTwoThree", StringHelper::toCamelCase($input1));

        $input2 = "One_Two_Three";
        assertEquals("oneTwoThree", StringHelper::toCamelCase($input2, "_"));

        $input3 = "one two three";
        assertEquals("oneTwoThree", StringHelper::toCamelCase($input3));
    }

    public function testToSnakeCase()
    {
        $input1 = "One Two Three";
        assertEquals("one_two_three", StringHelper::toSnakeCase($input1));

        $input2 = "One_Two_Three";
        assertEquals("one_two_three", StringHelper::toSnakeCase($input2, "_"));

        $input3 = "one two three";
        assertEquals("one_two_three", StringHelper::toSnakeCase($input3));
    }
}