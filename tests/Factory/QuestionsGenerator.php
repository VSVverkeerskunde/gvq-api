<?php declare(strict_types=1);

namespace VSV\GVQ_API\Factory;

use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Question\Models\Answers;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Models\Questions;
use VSV\GVQ_API\Question\ValueObjects\Year;

class QuestionsGenerator
{
    /**
     * @param Category $category
     * @return Questions
     * @throws \Exception
     */
    public static function generateForCategory(Category $category): Questions
    {
        $counter = 0;
        $questionArray = [];

        for ($i = 0; $i < 30; $i++) {
            $uuidSuffix = self::createUuidSuffix($counter, $category);

            $questionArray[] = new Question(
                Uuid::fromString('448c6bd8-0075-4302-a4de-'.$uuidSuffix),
                new Language('nl'),
                new Year(2018),
                $category,
                new NotEmptyString('Question '.$counter.'?'),
                new NotEmptyString('picture.jpg'),
                new Answers(
                    new Answer(
                        Uuid::fromString('73e6a2d0-3a50-4089-b84a-'.$uuidSuffix),
                        new PositiveNumber(1),
                        new NotEmptyString('Answer 1 to question '.$counter),
                        false
                    ),
                    new Answer(
                        Uuid::fromString('96bbb677-0839-46ae-9554-'.$uuidSuffix),
                        new PositiveNumber(2),
                        new NotEmptyString('Answer 2 to question '.$counter),
                        false
                    ),
                    new Answer(
                        Uuid::fromString('53780149-4ef9-405f-b4f4-'.$uuidSuffix),
                        new PositiveNumber(3),
                        new NotEmptyString('Answer 3 to question '.$counter),
                        true
                    )
                ),
                new NotEmptyString('Feedback '.$counter),
                new \DateTimeImmutable('2020-02-02T11:12:13+00:00')
            );

            $counter++;
        }

        return new Questions(...$questionArray);
    }

    /**
     * @param int $counter
     * @param Category $category
     * @return string
     */
    private static function createUuidSuffix(
        int $counter,
        Category $category
    ): string {
        $categorySuffix = substr($category->getId()->toString(), -4);
        $suffix = '00000000'.$counter.$categorySuffix;

        return substr($suffix, -12);
    }
}
