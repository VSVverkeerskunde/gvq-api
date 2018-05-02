<?php

namespace VSV\GVQ_API\Question;

use PHPUnit\Framework\TestCase;

class NotEmptyStringTest extends TestCase
{
    /**
     * @test
     */
    public function it_only_accepts_non_empty_argument()
    {
        $notEmptyString = new NotEmptyString('text');

        $this->assertNotNull($notEmptyString);
    }

    /**
     * @test
     */
    public function it_throws_on_empty_argument()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Text argument cannot be empty.');

        new NotEmptyString('');
    }
}
