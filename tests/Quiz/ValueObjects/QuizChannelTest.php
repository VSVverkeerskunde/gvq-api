<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\ValueObjects;

use PHPUnit\Framework\TestCase;

class QuizChannelTest extends TestCase
{
    /**
     * @var QuizChannel
     */
    private $quizChannel;

    protected function setUp(): void
    {
        $this->quizChannel = new QuizChannel('individual');
    }

    /**
     * @test
     * @dataProvider invalidChannelProvider
     * @param string $channel
     */
    public function it_throws_for_unsupported_values(string $channel): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid value "'.$channel.'" for VSV\GVQ_API\Quiz\ValueObjects\QuizChannel.'
        );

        new QuizChannel($channel);
    }

    /**
     * @return string [][]
     */
    public function invalidChannelProvider(): array
    {
        return [
            [
                'wrong',
            ],
            [
                'incorrect',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider validChannelProvider
     * @param string $channel
     */
    public function it_supports_allowed_values(string $channel): void
    {
        $this->assertNotNull(
            new QuizChannel($channel)
        );
    }

    /**
     * @return string[][]
     */
    public function validChannelProvider(): array
    {
        return [
            [
                'individual',
            ],
            [
                'company',
            ],
            [
                'partner',
            ],
        ];
    }

    /**
     * @test
     */
    public function it_supports_to_native(): void
    {
        $this->assertSame(
            'individual',
            $this->quizChannel->toNative()
        );
    }

    /**
     * @test
     * @dataProvider channelsProvider
     * @param QuizChannel $channel
     * @param QuizChannel $otherChannel
     * @param bool $expected
     */
    public function it_supports_equal_function(
        QuizChannel $channel,
        QuizChannel $otherChannel,
        bool $expected
    ): void {
        $this->assertEquals(
            $expected,
            $channel->equals($otherChannel)
        );
    }

    /**
     * @return array[]
     */
    public function channelsProvider(): array
    {
        return [
            [
                new QuizChannel('individual'),
                new QuizChannel('individual'),
                true,
            ],
            [
                new QuizChannel('individual'),
                new QuizChannel('company'),
                false,
            ],
        ];
    }
}
