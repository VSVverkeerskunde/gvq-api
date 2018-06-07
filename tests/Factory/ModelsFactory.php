<?php declare(strict_types=1);

namespace VSV\GVQ_API\Factory;

use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Models\TranslatedAlias;
use VSV\GVQ_API\Company\Models\TranslatedAliases;
use VSV\GVQ_API\Company\ValueObjects\Alias;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Question\Models\Answers;
use VSV\GVQ_API\Question\Models\Categories;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Models\Questions;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\User\Models\User;
use VSV\GVQ_API\User\ValueObjects\Email;
use VSV\GVQ_API\User\ValueObjects\Password;
use VSV\GVQ_API\User\ValueObjects\Role;

class ModelsFactory
{
    /**
     * @return Company
     */
    public static function createCompany(): Company
    {
        return new Company(
            Uuid::fromString('85fec50a-71ed-4d12-8a69-28a3cf5eb106'),
            new NotEmptyString('Vlaamse Stichting Verkeerskunde'),
            new PositiveNumber(49),
            self::createTranslatedAliases(),
            self::createUser()
        );
    }

    /**
     * @return Company
     */
    public static function createCompanyWithAlternateUser(): Company
    {
        return new Company(
            Uuid::fromString('85fec50a-71ed-4d12-8a69-28a3cf5eb106'),
            new NotEmptyString('Vlaamse Stichting Verkeerskunde'),
            new PositiveNumber(49),
            self::createTranslatedAliases(),
            self::createAlternateUser()
        );
    }

    /**
     * @return TranslatedAliases
     */
    public static function createTranslatedAliases(): TranslatedAliases
    {
        return new TranslatedAliases(
            self::createNlAlias(),
            self::createFrAlias()
        );
    }

    /**
     * @return TranslatedAlias
     */
    public static function createNlAlias(): TranslatedAlias
    {
        return new TranslatedAlias(
            Uuid::fromString('827a7945-ffd0-433e-b843-721c98ab72b8'),
            new Language('nl'),
            new Alias('vsv')
        );
    }

    /**
     * @return TranslatedAlias
     */
    public static function createFrAlias(): TranslatedAlias
    {
        return new TranslatedAlias(
            Uuid::fromString('f99c7747-7c27-4388-b0ec-dba380363d23'),
            new Language('fr'),
            new Alias('awsr')
        );
    }

    /**
     * @return User
     */
    public static function createUser(): User
    {
        return new User(
            Uuid::fromString('3ffc0f85-78ee-496b-bc61-17be1326c768'),
            new Email('john@gvq.be'),
            new NotEmptyString('Doe'),
            new NotEmptyString('John'),
            new Role('contact'),
            new Language('nl')
        );
    }

    /**
     * @return User
     */
    public static function createUserWithPassword(): User
    {
        return self::createUser()->withPassword(
            Password::fromHash('$2y$10$Hcfuxvnmk60VO0SKOsvQhuNBP/jJi6.eecdZnqVWCKVt8XNW7mEeO')
        );
    }

    /**
     * @return User
     */
    public static function createAlternateUser(): User
    {
        return new User(
            Uuid::fromString('0ffc0f85-78ee-496b-bc61-17be1326c768'),
            new Email('jane@gvq.be'),
            new NotEmptyString('Doe'),
            new NotEmptyString('Jane'),
            new Role('contact'),
            new Language('nl')
        );
    }

    /**
     * @return User
     */
    public static function createUpdatedUser(): User
    {
        return new User(
            Uuid::fromString('3ffc0f85-78ee-496b-bc61-17be1326c768'),
            new Email('jane@gvq.be'),
            new NotEmptyString('Doe'),
            new NotEmptyString('Jane'),
            new Role('contact'),
            new Language('nl')
        );
    }

    /**
     * @return Category
     */
    public static function createAccidentCategory(): Category
    {
        return new Category(
            Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0'),
            new NotEmptyString('EHBO/Ongeval/Verzekering')
        );
    }

    /**
     * @return Category
     */
    public static function createGeneralCategory(): Category
    {
        return new Category(
            Uuid::fromString('a7910bf1-05f9-4bdb-8dee-1256cbfafc0b'),
            new NotEmptyString('Algemene verkeersregels')
        );
    }

    /**
     * @return Categories
     */
    public static function createCategories(): Categories
    {
        return new Categories(
            self::createAccidentCategory(),
            self::createGeneralCategory()
        );
    }

    /**
     * @return Question
     */
    public static function createAccidentQuestion(): Question
    {
        return new Question(
            Uuid::fromString('448c6bd8-0075-4302-a4de-fe34d1554b8d'),
            new Language('fr'),
            new Year(2018),
            new Category(
                Uuid::fromString('1289d4b5-e88e-4b3c-9223-eb2c7c49f4d0'),
                new NotEmptyString('EHBO/Ongeval/Verzekering')
            ),
            new NotEmptyString(
                'La voiture devant vous roule très lentement. Pouvez-vous la dépasser par la gauche?'
            ),
            new NotEmptyString(
                'b746b623-a86f-4384-9ebc-51af80eb6bcc.jpg'
            ),
            new Answers(
                new Answer(
                    Uuid::fromString('73e6a2d0-3a50-4089-b84a-208092aeca8e'),
                    new NotEmptyString('Oui, mais uniquement en agglomération.'),
                    false
                ),
                new Answer(
                    Uuid::fromString('96bbb677-0839-46ae-9554-bcb709e49cab'),
                    new NotEmptyString('Non, on ne peut jamais rouler sur une voie ferrée.'),
                    false
                ),
                new Answer(
                    Uuid::fromString('53780149-4ef9-405f-b4f4-45e55fde3d67'),
                    new NotEmptyString('Non.'),
                    true
                )
            ),
            new NotEmptyString(
                'La voie publique située entre les deux lignes blanches continues est un site spécial franchissable.'
            )
        );
    }

    /**
     * @return Question
     */
    public static function createGeneralQuestion(): Question
    {
        return new Question(
            Uuid::fromString('5ffcac55-74e3-4836-a890-3e89a8a1cc15'),
            new Language('fr'),
            new Year(2018),
            new Category(
                Uuid::fromString('a7910bf1-05f9-4bdb-8dee-1256cbfafc0b'),
                new NotEmptyString('Algemene verkeersregels')
            ),
            new NotEmptyString(
                'Qui peut stationner devant ce garage?'
            ),
            new NotEmptyString(
                'a78593f7-2624-4894-aa51-d0c47b8660b8.jpg'
            ),
            new Answers(
                new Answer(
                    Uuid::fromString('c4d5fa4d-b5bc-4d92-a201-a84abb0e3253'),
                    new NotEmptyString('Les habitants de cette maison.'),
                    false
                ),
                new Answer(
                    Uuid::fromString('1ae8ea74-87f9-4e65-9458-d605888c3a54'),
                    new NotEmptyString('Personne.'),
                    false
                ),
                new Answer(
                    Uuid::fromString('a33daadb-be3f-4625-b1ae-368611680bde'),
                    new NotEmptyString('Les habitants de cette maison et leurs visiteurs.'),
                    true
                )
            ),
            new NotEmptyString(
                'Il est interdit de stationner devant l’entrée des propriétés.'
            )
        );
    }

    /**
     * @return Questions
     */
    public static function createQuestions(): Questions
    {
        return new Questions(
            self::createAccidentQuestion(),
            self::createGeneralQuestion()
        );
    }

    /**
     * @param string $model
     * @return string
     */
    public static function createJson(string $model): string
    {
        $jsonWithFormatting = file_get_contents(__DIR__.'/Samples/'.$model.'.json');
        $jsonAsArray = json_decode($jsonWithFormatting, true);

        return json_encode($jsonAsArray);
    }
}
