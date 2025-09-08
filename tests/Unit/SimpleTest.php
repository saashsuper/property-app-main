<?php

namespace Tests\Unit;

use Tests\TestCase;

class SimpleTest extends TestCase
{
    /** @test */
    public function it_can_run_a_simple_test()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_do_basic_math()
    {
        $result = 2 + 2;
        $this->assertEquals(4, $result);
    }

    /** @test */
    public function it_can_check_string_operations()
    {
        $string = 'Hello World';
        $this->assertStringContainsString('World', $string);
        $this->assertEquals(11, strlen($string));
    }
}
