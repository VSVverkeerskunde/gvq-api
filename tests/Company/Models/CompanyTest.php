<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Models;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Factory\ModelsFactory;

class CompanyTest extends TestCase
{
    /**
     * @var Company
     */
    private $company;

    protected function setUp(): void
    {
        $this->company = ModelsFactory::createCompany();
    }

    /**
     * @test
     * @dataProvider invalidAliasesProvider
     * @param TranslatedAliases $invalidAliases
     */
    public function it_throws_on_invalid_aliases(TranslatedAliases $invalidAliases): void
    {
        $suppliedValues = [];
        foreach ($invalidAliases as $alias) {
            $suppliedValues[] = $alias->getAlias()->toNative().' - '.$alias->getLanguage()->toNative();
        }

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid value(s) for aliases: '.implode($suppliedValues).
            'exactly one alias per language (nl and fr) required.');

        new Company(
            Uuid::fromString('85fec50a-71ed-4d12-8a69-28a3cf5eb106'),
            new NotEmptyString('Company Name'),
            $invalidAliases
        );
    }

    /**
     * @return TranslatedAliases[][]
     */
    public function invalidAliasesProvider(): array
    {
        return [
            [
                new TranslatedAliases(
                    ModelsFactory::createNlAlias(),
                    ModelsFactory::createNlAlias()
                ),
            ],
            [
                new TranslatedAliases(
                    ModelsFactory::createNlAlias()
                ),
            ],
        ];
    }


    /**
     * @test
     */
    public function it_can_store_an_id(): void
    {
        $this->assertEquals(
            Uuid::fromString('85fec50a-71ed-4d12-8a69-28a3cf5eb106'),
            $this->company->getId()
        );
    }

    /**
     * @test
     */
    public function it_can_store_a_name(): void
    {
        $this->assertEquals(
            new NotEmptyString('Company Name'),
            $this->company->getName()
        );
    }

    /**
     * @test
     */
    public function it_can_store_aliases(): void
    {
        $this->assertEquals(
            ModelsFactory::createTranslatedAliases(),
            $this->company->getAliases()
        );
    }
}
