<?php declare(strict_types=1);

namespace VSV\GVQ_API\Contest\ValueObjects;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\User\ValueObjects\Email;

class ContestParticipantTest extends TestCase
{
    /**
     * @var ContestParticipant
     */
    private $contestParticipant;

    /**
     * @throws \Exception
     */
    protected function setUp()
    {
        $this->contestParticipant = ModelsFactory::createContestParticipant();
    }

    /**
     * @test
     */
    public function it_stores_an_email(): void
    {
        $this->assertEquals(
            new Email('jane@gvq.be'),
            $this->contestParticipant->getEmail()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_first_name(): void
    {
        $this->assertEquals(
            new NotEmptyString('Jane'),
            $this->contestParticipant->getFirstName()
        );
    }

    /**
     * @test
     */
    public function it_stores_a_last_name(): void
    {
        $this->assertEquals(
            new NotEmptyString('Doe'),
            $this->contestParticipant->getLastName()
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_stores_a_date_of_birth(): void
    {
        $this->assertEquals(
            new \DateTimeImmutable('1980-01-01T11:12:13+00:00'),
            $this->contestParticipant->getDateOfBirth()
        );
    }
}
