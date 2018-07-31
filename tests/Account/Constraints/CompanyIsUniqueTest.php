<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Constraints;

use PHPUnit\Framework\TestCase;

class CompanyIsUniqueTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_the_correct_validator_class(): void
    {
        $uniqueCompanyConstraint = new CompanyIsUnique();

        $this->assertEquals(
            CompanyIsUniqueValidator::class,
            $uniqueCompanyConstraint->validatedBy()
        );
    }
}
