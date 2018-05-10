<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Models;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Question\ValueObjects\NotEmptyString;

class CategoriesTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_iterate_over_categories(): void
    {
        $category1 = new Category(
            Uuid::fromString('0b62cb1d-06a1-43c8-a282-6589d40c9b93'),
            new NotEmptyString('EHBO/Ongeval/Verzekering')
        );

        $category2 = new Category(
            Uuid::fromString('a7910bf1-05f9-4bdb-8dee-1256cbfafc0b'),
            new NotEmptyString('Algemene verkeersregels')
        );

        $expectedCategories = [
            $category1,
            $category2,
        ];

        $categories = new Categories(...$expectedCategories);

        $actualCategories = [];
        foreach ($categories as $category) {
            $actualCategories[] = $category;
        }

        $this->assertEquals($expectedCategories, $actualCategories);
    }
}
