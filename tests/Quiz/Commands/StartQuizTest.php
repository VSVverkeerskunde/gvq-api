<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Commands;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\ValueObjects\Alias;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Quiz\ValueObjects\QuizParticipant;
use VSV\GVQ_API\User\ValueObjects\Email;

class StartQuizTest extends TestCase
{
    /**
     * @var StartQuiz
     */
    private $startQuiz;

    protected function setUp(): void
    {
        $this->startQuiz = new StartQuiz(
            new QuizParticipant(new Email('par@ticipa.nt')),
            new QuizChannel(QuizChannel::COMPANY),
            new Alias('vsv'),
            new Alias('dats'),
            Uuid::fromString('9c2c62c3-655a-4444-89e5-6c493cf2c684'),
            new Language(Language::NL)
        );
    }

    /**
     * @test
     */
    public function it_stores_a_quiz_participant()
    {
        $this->assertEquals(
            new QuizParticipant(new Email('par@ticipa.nt')),
            $this->startQuiz->getParticipant()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_quiz_channel()
    {
        $this->assertEquals(
            new QuizChannel(QuizChannel::COMPANY),
            $this->startQuiz->getQuizChannel()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_company_alias()
    {
        $this->assertEquals(
            new Alias('vsv'),
            $this->startQuiz->getCompanyAlias()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_partner_alias()
    {
        $this->assertEquals(
            new Alias('dats'),
            $this->startQuiz->getPartnerAlias()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_team_id()
    {
        $this->assertEquals(
            Uuid::fromString('9c2c62c3-655a-4444-89e5-6c493cf2c684'),
            $this->startQuiz->getTeamId()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_language()
    {
        $this->assertEquals(
            new Language(Language::NL),
            $this->startQuiz->getLanguage()
        );
    }
}
