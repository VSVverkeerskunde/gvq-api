<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Constraints;

use PHPUnit\Framework\TestCase;

class AliasIsUniqueTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_the_correct_validator_class(): void
    {
        $uniqueAliasConstraint = new AliasIsUnique();

        $this->assertEquals(
            AliasIsUniqueValidator::class,
            $uniqueAliasConstraint->validatedBy()
        );
    }
}
