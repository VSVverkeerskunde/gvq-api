<?php

namespace VSV\GVQ_API\Question;

use PHPUnit\Framework\TestCase;

class NotEmptyStringTest extends TestCase
{
    /**
     * @var NotEmptyString
     */
    private $notEmptyString;

    /**
     * @var string
     */
    private $value = 'text';


    protected function setUp()
    {
        $this->notEmptyString = new NotEmptyString($this->value);
    }

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

    /**
     * @test
     */
    public function it_supports_to_native()
    {
        $this->assertEquals($this->value, $this->notEmptyString->toNative());
    }

    /**
     * @test
     */
    public function it_supports_to_string()
    {
        $this->assertEquals($this->value, $this->notEmptyString->__toString());
    }

    /**
     * @test
     * @dataProvider notEmptyStringsProvider
     * @param NotEmptyString $notEmptyString
     * @param NotEmptyString $otherNotEmptyString
     * @param bool $expected
     */
    public function it_supports_equals_function(
        NotEmptyString $notEmptyString,
        NotEmptyString $otherNotEmptyString,
        bool $expected
    ) {
        $this->assertEquals($expected, $notEmptyString->equals($otherNotEmptyString));
    }

    /**
     * @return array[]
     */
    public function notEmptyStringsProvider()
    {
        return [
            [
                new NotEmptyString('text'),
                new NotEmptyString('text'),
                true,
            ],
            [
                new NotEmptyString('text'),
                new NotEmptyString('text2'),
                false,
            ],
        ];
    }
}
