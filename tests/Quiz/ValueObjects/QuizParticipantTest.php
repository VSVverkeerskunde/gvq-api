<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\ValueObjects;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\User\ValueObjects\Email;

class QuizParticipantTest extends TestCase
{

    /**
     * @var QuizParticipant
     */
    private $quizParticipant;

    protected function setUp(): void
    {
        $this->quizParticipant = new QuizParticipant(new Email('par@ticipa.nt'));
    }

    /**
     * @test
     * @dataProvider participantsProvider
     * @param QuizParticipant $participant
     * @param QuizParticipant $otherParticipant
     * @param bool $expected
     */
    public function it_supports_equal_function(
        QuizParticipant $participant,
        QuizParticipant $otherParticipant,
        bool $expected
    ): void {
        $this->assertEquals(
            $expected,
            $participant->equals($otherParticipant)
        );
    }

    /**
     * @return array[]
     */
    public function participantsProvider(): array
    {
        return [
            [
                new QuizParticipant(new Email('par@ticipa.nt')),
                new QuizParticipant(new Email('par@ticipa.nt')),
                true,
            ],
            [
                new QuizParticipant(new Email('par@ticipa.nt')),
                new QuizParticipant(new Email('otherpar@ticipa.nt')),
                false,
            ],
        ];
    }
}
