<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Models;

use PHPUnit\Framework\TestCase;
use VSV\GVQ_API\Factory\ModelsFactory;

class QuizTest extends TestCase
{
    /**
     * @test
     * @throws \Exception
     */
    public function it_throws_on_creating_cup_with_partner(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage('Quiz of channel partner can not be of type cup');

        ModelsFactory::createCupWithPartner();
    }
}
