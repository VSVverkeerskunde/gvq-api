<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Constraints;

use PHPUnit\Framework\TestCase;

class UserIsUniqueTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_the_correct_validator_class(): void
    {
        $uniqeUserConstraint = new UserIsUnique();

        $this->assertEquals(
            'VSV\GVQ_API\Account\Constraints\UserIsUniqueValidator',
            $uniqeUserConstraint->validatedBy()
        );
    }
}
