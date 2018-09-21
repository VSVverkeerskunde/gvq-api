<?php declare(strict_types=1);

namespace VSV\GVQ_API\Factory;

use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Models\TranslatedAlias;
use VSV\GVQ_API\Company\Models\TranslatedAliases;
use VSV\GVQ_API\Company\ValueObjects\Alias;
use VSV\GVQ_API\Company\ValueObjects\PositiveNumber;
use VSV\GVQ_API\Contest\Models\ContestParticipation;
use VSV\GVQ_API\Contest\Models\TieBreaker;
use VSV\GVQ_API\Contest\ValueObjects\Address;
use VSV\GVQ_API\Contest\ValueObjects\ContestParticipant;
use VSV\GVQ_API\Mail\Models\Sender;
use VSV\GVQ_API\Partner\Models\Partner;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Question\Models\Answers;
use VSV\GVQ_API\Question\Models\Categories;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Models\Questions;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\Commands\StartQuiz;
use VSV\GVQ_API\Quiz\Events\AnsweredCorrect;
use VSV\GVQ_API\Quiz\Events\AnsweredIncorrect;
use VSV\GVQ_API\Quiz\Events\AnsweredTooLate;
use VSV\GVQ_API\Quiz\Events\QuestionAsked;
use VSV\GVQ_API\Quiz\Events\QuizFinished;
use VSV\GVQ_API\Quiz\Events\QuizStarted;
use VSV\GVQ_API\Quiz\Models\Quiz;
use VSV\GVQ_API\Quiz\ValueObjects\AllowedDelay;
use VSV\GVQ_API\Quiz\ValueObjects\QuestionResult;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Quiz\ValueObjects\QuizParticipant;
use VSV\GVQ_API\Registration\Models\Registration;
use VSV\GVQ_API\Registration\ValueObjects\UrlSuffix;
use VSV\GVQ_API\Statistics\Models\EmployeeParticipation;
use VSV\GVQ_API\Statistics\ValueObjects\Average;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
use VSV\GVQ_API\Statistics\ValueObjects\TeamScore;
use VSV\GVQ_API\Statistics\ValueObjects\TeamScores;
use VSV\GVQ_API\Team\Models\Team;
use VSV\GVQ_API\Team\Models\Teams;
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
    public static function createAwsrCompany(): Company
    {
        return new Company(
            Uuid::fromString('6e25425c-77cd-4899-9bfd-c2b8defb339f'),
            new NotEmptyString('AWSR'),
            new PositiveNumber(10),
            self::createTranslatedAliases(),
            self::createUser()
        );
    }

    /**
     * @return Company
     */
    public static function createUpdatedCompany(): Company
    {
        return new Company(
            Uuid::fromString('85fec50a-71ed-4d12-8a69-28a3cf5eb106'),
            new NotEmptyString('VSV'),
            new PositiveNumber(51),
            self::createTranslatedAliases(),
            self::createUser()
        );
    }

    /**
     * @return Company
     */
    public static function createAlternateCompany(): Company
    {
        return new Company(
            Uuid::fromString('c0dc2df1-a09e-4393-94c2-20319af53e2b'),
            new NotEmptyString('Agence wallonne pour la Sécurité routière'),
            new PositiveNumber(23),
            self::createTranslatedAliases(),
            self::createAlternateUser()
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
            new Language('nl'),
            true
        );
    }

    /**
     * @return User
     */
    public static function createInactiveUser(): User
    {
        return new User(
            Uuid::fromString('3ffc0f85-78ee-496b-bc61-17be1326c768'),
            new Email('john@gvq.be'),
            new NotEmptyString('Doe'),
            new NotEmptyString('John'),
            new Role('contact'),
            new Language('nl'),
            false
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
    public static function createUserWithAlternatePassword(): User
    {
        return self::createUser()->withPassword(
            Password::fromPlainText('newPassw0rD')
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
            new Language('nl'),
            true
        );
    }

    /**
     * @return User
     */
    public static function createFrenchUser(): User
    {
        return new User(
            Uuid::fromString('39201b68-ec61-471e-ab5e-2e8665c5a776'),
            new Email('academie@francais.be'),
            new NotEmptyString('Français'),
            new NotEmptyString('Académie'),
            new Role('contact'),
            new Language('nl'),
            true
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
            new Language('nl'),
            true
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

    public static function createAllCategories(): Categories
    {
        return new Categories(
            self::createGeneralCategory(),
            new Category(
                Uuid::fromString('15530c78-2b1c-4820-bcfb-e04c5e2224b9'),
                new NotEmptyString('Kwetsbare weggebruikers')
            ),
            new Category(
                Uuid::fromString('67844067-82ca-4698-a713-b5fbd4c729c5'),
                new NotEmptyString('Verkeerstekens')
            ),
            new Category(
                Uuid::fromString('58ee6bd3-a3f4-42bc-ba81-82491cec55b9'),
                new NotEmptyString('Voorrang')
            ),
            self::createAccidentCategory(),
            new Category(
                Uuid::fromString('9677995d-5fc5-48cd-a251-565b626cb7c1'),
                new NotEmptyString('Voertuig/Technieks')
            ),
            new Category(
                Uuid::fromString('fce11f3c-24ad-4aed-b00d-0069e3404749'),
                new NotEmptyString('Openbaar vervoer/Milieu')
            ),
            new Category(
                Uuid::fromString('6f0c9e04-1e84-4ba4-be54-ab5747111754'),
                new NotEmptyString('Verkeersveiligheid')
            )
        );
    }

    /**
     * @param UuidInterface $uuid
     * @param \DateTimeImmutable $createdOn
     * @return Question
     */
    private static function createAccidentQuestionFactory(
        UuidInterface $uuid,
        \DateTimeImmutable $createdOn
    ): Question {
        return new Question(
            $uuid,
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
                    new PositiveNumber(1),
                    new NotEmptyString('Oui, mais uniquement en agglomération.'),
                    false
                ),
                new Answer(
                    Uuid::fromString('96bbb677-0839-46ae-9554-bcb709e49cab'),
                    new PositiveNumber(2),
                    new NotEmptyString('Non, on ne peut jamais rouler sur une voie ferrée.'),
                    false
                ),
                new Answer(
                    Uuid::fromString('53780149-4ef9-405f-b4f4-45e55fde3d67'),
                    new PositiveNumber(3),
                    new NotEmptyString('Non.'),
                    true
                )
            ),
            new NotEmptyString(
                'La voie publique située entre les deux lignes blanches continues est un site spécial franchissable.'
            ),
            $createdOn
        );
    }

    /**
     * @return Question
     * @throws \Exception
     */
    public static function createAccidentQuestion(): Question
    {
        return self::createAccidentQuestionFactory(
            Uuid::fromString('448c6bd8-0075-4302-a4de-fe34d1554b8d'),
            new \DateTimeImmutable('2020-02-02T11:12:13+00:00')
        );
    }

    /**
     * @param \DateTimeImmutable $createdOn
     * @return Question
     * @throws \Exception
     */
    public static function createAccidentQuestionWithCreatedOn(
        \DateTimeImmutable $createdOn
    ): Question {
        return self::createAccidentQuestionFactory(
            Uuid::fromString('9e316101-4c99-473e-ae2d-2fcb8674da0a'),
            $createdOn
        );
    }

    /**
     * @return Question
     * @throws \Exception
     */
    public static function createArchivedAccidentQuestion(): Question
    {
        $question = self::createAccidentQuestion();
        $question->archiveOn(new \DateTimeImmutable('2020-02-02T14:12:13+00:00'));

        return $question;
    }

    /**
     * @return Question
     * @throws \Exception
     */
    public static function createUpdatedAccidentQuestion(): Question
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
                'Qui peut stationner devant ce garage?'
            ),
            new NotEmptyString(
                'b746b623-a86f-4384-9ebc-51af80eb6bcc.jpg'
            ),
            new Answers(
                new Answer(
                    Uuid::fromString('73e6a2d0-3a50-4089-b84a-208092aeca8e'),
                    new PositiveNumber(1),
                    new NotEmptyString('Oui, mais uniquement en agglomération.'),
                    false
                ),
                new Answer(
                    Uuid::fromString('96bbb677-0839-46ae-9554-bcb709e49cab'),
                    new PositiveNumber(2),
                    new NotEmptyString('Non, on ne peut jamais rouler sur une voie ferrée.'),
                    false
                ),
                new Answer(
                    Uuid::fromString('53780149-4ef9-405f-b4f4-45e55fde3d67'),
                    new PositiveNumber(3),
                    new NotEmptyString('Non.'),
                    true
                )
            ),
            new NotEmptyString(
                'La voie publique située entre les deux lignes blanches continues est un site spécial franchissable.'
            ),
            new \DateTimeImmutable('2020-02-02T11:12:13+00:00')
        );
    }

    /**
     * @return Question
     * @throws \Exception
     */
    public static function createUpdatedAccidentQuestionWithRemovedAnswer(): Question
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
                'Qui peut stationner devant ce garage?'
            ),
            new NotEmptyString(
                'b746b623-a86f-4384-9ebc-51af80eb6bcc.jpg'
            ),
            new Answers(
                new Answer(
                    Uuid::fromString('73e6a2d0-3a50-4089-b84a-208092aeca8e'),
                    new PositiveNumber(1),
                    new NotEmptyString('Oui, mais uniquement en agglomération.'),
                    true
                ),
                new Answer(
                    Uuid::fromString('96bbb677-0839-46ae-9554-bcb709e49cab'),
                    new PositiveNumber(2),
                    new NotEmptyString('Non, on ne peut jamais rouler sur une voie ferrée.'),
                    false
                )
            ),
            new NotEmptyString(
                'La voie publique située entre les deux lignes blanches continues est un site spécial franchissable.'
            ),
            new \DateTimeImmutable('2020-02-02T11:12:13+00:00')
        );
    }

    /**
     * @return Question
     * @throws \Exception
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
                    new PositiveNumber(1),
                    new NotEmptyString('Les habitants de cette maison.'),
                    false
                ),
                new Answer(
                    Uuid::fromString('1ae8ea74-87f9-4e65-9458-d605888c3a54'),
                    new PositiveNumber(2),
                    new NotEmptyString('Personne.'),
                    false
                ),
                new Answer(
                    Uuid::fromString('a33daadb-be3f-4625-b1ae-368611680bde'),
                    new PositiveNumber(3),
                    new NotEmptyString('Les habitants de cette maison et leurs visiteurs.'),
                    true
                )
            ),
            new NotEmptyString(
                'Il est interdit de stationner devant l’entrée des propriétés.'
            ),
            new \DateTimeImmutable('2020-02-02T13:12:13+01:00')
        );
    }

    /**
     * @return Question
     * @throws \Exception
     */
    public static function createQuestionWithMissingCategory(): Question
    {
        $missingCategory = self::createMissingCategory();

        $question = new Question(
            Uuid::fromString('448c6bd8-0075-4302-a4de-fe34d1554b8d'),
            new Language('fr'),
            new Year(2018),
            $missingCategory,
            new NotEmptyString(
                'La voiture devant vous roule très lentement. Pouvez-vous la dépasser par la gauche?'
            ),
            new NotEmptyString(
                'b746b623-a86f-4384-9ebc-51af80eb6bcc.jpg'
            ),
            new Answers(
                new Answer(
                    Uuid::fromString('73e6a2d0-3a50-4089-b84a-208092aeca8e'),
                    new PositiveNumber(1),
                    new NotEmptyString('Oui, mais uniquement en agglomération.'),
                    false
                ),
                new Answer(
                    Uuid::fromString('96bbb677-0839-46ae-9554-bcb709e49cab'),
                    new PositiveNumber(2),
                    new NotEmptyString('Non, on ne peut jamais rouler sur une voie ferrée.'),
                    false
                ),
                new Answer(
                    Uuid::fromString('53780149-4ef9-405f-b4f4-45e55fde3d67'),
                    new PositiveNumber(3),
                    new NotEmptyString('Non.'),
                    true
                )
            ),
            new NotEmptyString(
                'La voie publique située entre les deux lignes blanches continues est un site spécial franchissable.'
            ),
            new \DateTimeImmutable('2020-02-02T11:12:13+00:00')
        );

        return $question;
    }

    /**
     * @return Category
     */
    public static function createMissingCategory(): Category
    {
        return new Category(
            Uuid::fromString('0289d4b5-e88e-4b3c-9223-eb2c7c49f4d0'),
            new NotEmptyString('EHBO/Ongeval/Verzekering')
        );
    }

    /**
     * @return Questions
     * @throws \Exception
     */
    public static function createQuestions(): Questions
    {
        return new Questions(
            self::createAccidentQuestion(),
            self::createGeneralQuestion()
        );
    }

    /**
     * @return Registration
     * @throws \Exception
     */
    public static function createRegistration(): Registration
    {
        return new Registration(
            Uuid::fromString('00f20af9-c2f5-4bfb-9424-5c0c29fbc2e3'),
            new UrlSuffix('d2c63a605ae27c13e43e26fe2c97a36c4556846dd3ef'),
            self::createUser(),
            new \DateTimeImmutable('2020-02-02T11:12:13+00:00'),
            false
        );
    }

    /**
     * @return Registration
     * @throws \Exception
     */
    public static function createPasswordRequest(): Registration
    {
        return new Registration(
            Uuid::fromString('00f20af9-c2f5-4bfb-9424-5c0c29fbc2e3'),
            new UrlSuffix('d2c63a605ae27c13e43e26fe2c97a36c4556846dd3ef'),
            self::createUser(),
            new \DateTimeImmutable('2020-02-02T11:12:13+00:00'),
            true
        );
    }

    /**
     * @return Registration
     * @throws \Exception
     */
    public static function createRegistrationWithAlternateUser(): Registration
    {
        return new Registration(
            Uuid::fromString('00f20af9-c2f5-4bfb-9424-5c0c29fbc2e3'),
            new UrlSuffix('d2c63a605ae27c13e43e26fe2c97a36c4556846dd3ef'),
            self::createAlternateUser(),
            new \DateTimeImmutable('2020-02-02T11:12:13+00:00'),
            false
        );
    }

    /**
     * @return Sender
     */
    public static function createSenderNl(): Sender
    {
        return new Sender(
            new Email('quiz@vsv.be'),
            new NotEmptyString('Grote verkeersquiz 2018'),
            new Language('nl')
        );
    }

    /**
     * @return Sender
     */
    public static function createSenderFr(): Sender
    {
        return new Sender(
            new Email('quiz@awsr.be'),
            new NotEmptyString('Quiz de la Route 2018'),
            new Language('fr')
        );
    }

    /**
     * @return Quiz
     * @throws \Exception
     */
    public static function createIndividualQuiz(): Quiz
    {
        return self::createCustomQuiz(
            Uuid::fromString('f604152c-3cc5-4888-be87-af371ac3aa6b'),
            new QuizChannel(QuizChannel::INDIVIDUAL),
            null,
            null,
            null
        );
    }

    /**
     * @return Quiz
     * @throws \Exception
     */
    public static function createCompanyQuiz(): Quiz
    {
        return self::createCustomQuiz(
            Uuid::fromString('f604152c-3cc5-4888-be87-af371ac3aa6b'),
            new QuizChannel(QuizChannel::COMPANY),
            self::createCompany(),
            null,
            null
        );
    }

    /**
     * @return Quiz
     * @throws \Exception
     */
    public static function createPartnerQuiz(): Quiz
    {
        return self::createCustomQuiz(
            Uuid::fromString('68e6585d-96ba-48d4-ac4b-dca103f2280b'),
            new QuizChannel(QuizChannel::PARTNER),
            null,
            self::createDatsPartner(),
            null
        );
    }

    /**
     * @return Quiz
     * @throws \Exception
     */
    public static function createCupQuiz(): Quiz
    {
        return self::createCustomQuiz(
            Uuid::fromString('5f677528-1700-4fac-9e57-1718d1c3e667'),
            new QuizChannel(QuizChannel::CUP),
            null,
            null,
            self::createAntwerpTeam()
        );
    }

    /**
     * @param UuidInterface $uuid
     * @param QuizChannel $channel
     * @param Company|null $company
     * @param Partner|null $partner
     * @param Team|null $team
     * @param Language|null $language
     * @return Quiz
     * @throws \Exception
     */
    public static function createCustomQuiz(
        UuidInterface $uuid,
        QuizChannel $channel,
        ?Company $company,
        ?Partner $partner,
        ?Team $team,
        Language $language = null
    ): Quiz {
        return new Quiz(
            $uuid,
            new QuizParticipant(new Email('par@ticipa.nt')),
            $channel,
            $company,
            $partner,
            $team,
            $language ? $language : new Language('nl'),
            new Year(2018),
            new AllowedDelay(40),
            self::createQuestions()
        );
    }

    /**
     * @return Partner
     */
    public static function createNBPartner(): Partner
    {
        return new Partner(
            Uuid::fromString('b00bfa30-97e4-4972-bd65-24b371f75718'),
            new NotEmptyString('Nieuwsblad'),
            new Alias('nieuwsblad')
        );
    }

    /**
     * @return Partner
     */
    public static function createDatsPartner(): Partner
    {
        return new Partner(
            Uuid::fromString('adf0796d-4f9f-470e-9bbe-17d4d9c900cd'),
            new NotEmptyString('Dats24'),
            new Alias('dats24')
        );
    }

    /**
     * @return Partner
     */
    public static function createSudPressePartner(): Partner
    {
        return new Partner(
            Uuid::fromString('2f763a5c-32bb-4dec-ad21-835546ff7c25'),
            new NotEmptyString('SudPresse'),
            new Alias('sudpresse')
        );
    }

    /**
     * @return Team
     */
    public static function createAntwerpTeam(): Team
    {
        return new Team(
            Uuid::fromString('5c128cad-8727-4e3e-bfba-c51929ae14c4'),
            new NotEmptyString('Royal Antwerp FC')
        );
    }

    /**
     * @return Team
     */
    public static function createLeuvenTeam(): Team
    {
        return new Team(
            Uuid::fromString('9c2c62c3-655a-4444-89e5-6c493cf2c684'),
            new NotEmptyString('OH Leuven')
        );
    }

    /**
     * @return Team
     */
    public static function createWaaslandTeam(): Team
    {
        return new Team(
            Uuid::fromString('72206a00-4c5a-407d-b1de-5c8cc0806d54'),
            new NotEmptyString('Waasland-Beveren')
        );
    }

    /**
     * @return Team
     */
    public static function createTubizeTeam(): Team
    {
        return new Team(
            Uuid::fromString('e36005ae-9e83-4620-8484-d03ce0106b2e'),
            new NotEmptyString('AFC Tubize')
        );
    }

    /**
     * @return Team
     */
    public static function createLommelTeam(): Team
    {
        return new Team(
            Uuid::fromString('924f1974-6eff-4ad2-abb2-d5d38826d884'),
            new NotEmptyString('Lommel SK')
        );
    }

    /**
     * @return Team
     */
    public static function createRoeselareTeam(): Team
    {
        return new Team(
            Uuid::fromString('4224d2c4-7ba7-4ff8-901e-f4265d24b09d'),
            new NotEmptyString('KSV Roeselare')
        );
    }

    /**
     * @return Team
     */
    public static function createBruggeTeam(): Team
    {
        return new Team(
            Uuid::fromString('922391c4-fc5b-4148-b69d-d347d48caaef'),
            new NotEmptyString('Club Brugge KV')
        );
    }

    public static function createTeams(): Teams
    {
        return new Teams(
            self::createAntwerpTeam(),
            self::createLeuvenTeam(),
            self::createWaaslandTeam(),
            self::createTubizeTeam(),
            self::createLommelTeam(),
            self::createRoeselareTeam()
        );
    }

    /**
     * @return TeamScore
     */
    public static function createAntwerpTeamScore(): TeamScore
    {
        return new TeamScore(
            self::createAntwerpTeam(),
            new NaturalNumber(10),
            new NaturalNumber(3),
            new Average(4.4375)
        );
    }

    /**
     * @return TeamScore
     */
    public static function createLeuvenTeamScore(): TeamScore
    {
        return new TeamScore(
            self::createLeuvenTeam(),
            new NaturalNumber(16),
            new NaturalNumber(2),
            new Average(8.575)
        );
    }

    /**
     * @return TeamScore
     */
    public static function createWaaslandTeamScore(): TeamScore
    {
        return new TeamScore(
            self::createWaaslandTeam(),
            new NaturalNumber(0),
            new NaturalNumber(0),
            new Average(1.1875)
        );
    }

    /**
     * @return TeamScore
     */
    public static function createTubizeTeamScore(): TeamScore
    {
        return new TeamScore(
            self::createTubizeTeam(),
            new NaturalNumber(0),
            new NaturalNumber(0),
            new Average(1.25)
        );
    }

    /**
     * @return TeamScore
     */
    public static function createLommelTeamScore(): TeamScore
    {
        return new TeamScore(
            self::createLommelTeam(),
            new NaturalNumber(10),
            new NaturalNumber(3),
            new Average(4.5)
        );
    }

    /**
     * @return TeamScore
     */
    public static function createRoeselareTeamScore(): TeamScore
    {
        return new TeamScore(
            self::createRoeselareTeam(),
            new NaturalNumber(3),
            new NaturalNumber(1),
            new Average(4.0125)
        );
    }

    /**
     * @return TeamScore
     */
    public static function createBruggeTeamScore(): TeamScore
    {
        return new TeamScore(
            self::createBruggeTeam(),
            new NaturalNumber(3),
            new NaturalNumber(1),
            new Average(4.0125)
        );
    }

    /**
     * @return TeamScores
     */
    public static function createTeamScores(): TeamScores
    {
        return new TeamScores(
            self::createLeuvenTeamScore(),
            self::createLommelTeamScore(),
            self::createAntwerpTeamScore(),
            self::createRoeselareTeamScore(),
            self::createTubizeTeamScore(),
            self::createWaaslandTeamScore()
        );
    }

    /**
     * @param Team $team
     * @param NaturalNumber $totalScore
     * @param NaturalNumber $participationCount
     * @param Average $rankingScore
     * @return TeamScore
     */
    public static function createCustomTeamScore(
        Team $team,
        NaturalNumber $totalScore,
        NaturalNumber $participationCount,
        Average $rankingScore
    ): TeamScore {
        return new TeamScore(
            $team,
            $totalScore,
            $participationCount,
            $rankingScore
        );
    }

    /**
     * @return QuizStarted
     * @throws \Exception
     */
    public static function createQuizStarted(): QuizStarted
    {
        return new QuizStarted(
            Uuid::fromString('eb7eb3bc-4d1f-4d40-817f-fba705aa8e49'),
            ModelsFactory::createIndividualQuiz()
        );
    }

    /**
     * @return StartQuiz
     */
    public static function createStartQuiz(): StartQuiz
    {
        return new StartQuiz(
            new QuizParticipant(new Email('par@ticipa.nt')),
            new QuizChannel(QuizChannel::COMPANY),
            new Alias('vsv'),
            new Alias('dats'),
            Uuid::fromString('9c2c62c3-655a-4444-89e5-6c493cf2c684'),
            new Language(Language::NL)
        );
    }

    /**
     * @return QuestionAsked
     * @throws \Exception
     */
    public static function createQuestionAsked(): QuestionAsked
    {
        return new QuestionAsked(
            Uuid::fromString('366f4484-78d5-4051-9a6f-79c3e00589c6'),
            ModelsFactory::createAccidentQuestion(),
            new \DateTimeImmutable('2020-11-11T11:12:13+00:00')
        );
    }

    /**
     * @return AnsweredCorrect
     * @throws \Exception
     */
    public static function createAnsweredCorrect(): AnsweredCorrect
    {
        return new AnsweredCorrect(
            Uuid::fromString('366f4484-78d5-4051-9a6f-79c3e00589c6'),
            ModelsFactory::createAccidentQuestion(),
            new Answer(
                Uuid::fromString('53780149-4ef9-405f-b4f4-45e55fde3d67'),
                new PositiveNumber(3),
                new NotEmptyString('Non.'),
                true
            ),
            new \DateTimeImmutable('2020-11-11T11:12:33+00:00')
        );
    }

    /**
     * @return AnsweredIncorrect
     * @throws \Exception
     */
    public static function createAnsweredIncorrect(): AnsweredIncorrect
    {
        return new AnsweredIncorrect(
            Uuid::fromString('366f4484-78d5-4051-9a6f-79c3e00589c6'),
            ModelsFactory::createAccidentQuestion(),
            new Answer(
                Uuid::fromString('96bbb677-0839-46ae-9554-bcb709e49cab'),
                new PositiveNumber(2),
                new NotEmptyString('Non, on ne peut jamais rouler sur une voie ferrée.'),
                false
            ),
            new \DateTimeImmutable('2020-11-11T11:12:33+00:00')
        );
    }

    /**
     * @return AnsweredTooLate
     * @throws \Exception
     */
    public static function createAnsweredTooLate(): AnsweredTooLate
    {
        return new AnsweredTooLate(
            Uuid::fromString('366f4484-78d5-4051-9a6f-79c3e00589c6'),
            ModelsFactory::createAccidentQuestion(),
            new \DateTimeImmutable('2020-11-11T11:12:33+00:00')
        );
    }

    /**
     * @return QuizFinished
     */
    public static function createQuizFinished(): QuizFinished
    {
        return new QuizFinished(
            Uuid::fromString('366f4484-78d5-4051-9a6f-79c3e00589c6'),
            2
        );
    }

    /**
     * @return QuestionResult
     * @throws \Exception
     */
    public static function createQuestionResult(): QuestionResult
    {
        return self::createCustomQuestionResult(
            ModelsFactory::createAccidentQuestion(),
            true,
            11
        );
    }

    /**
     * @param Question $question
     * @param bool|null $answeredTooLate
     * @param int|null $score
     * @return QuestionResult
     */
    public static function createCustomQuestionResult(
        Question $question,
        ?bool $answeredTooLate,
        ?int $score
    ): QuestionResult {
        return new QuestionResult(
            $question,
            $answeredTooLate,
            $score
        );
    }

    /**
     * @return TieBreaker
     */
    public static function createQuizTieBreaker(): TieBreaker
    {
        return new TieBreaker(
            Uuid::fromString('72a90e90-d54e-48f4-b29d-32e88e06b86c'),
            new Year(2018),
            new QuizChannel(QuizChannel::INDIVIDUAL),
            new Language('nl'),
            new NotEmptyString('Hoeveel van hen behaalden 11/15 of meer?'),
            new PositiveNumber(14564)
        );
    }

    /**
     * @return TieBreaker
     */
    public static function createCupTieBreaker(): TieBreaker
    {
        return new TieBreaker(
            Uuid::fromString('f6e4bcf3-e069-4d40-8b72-2f5961ec31b5'),
            new Year(2018),
            new QuizChannel(QuizChannel::CUP),
            new Language('nl'),
            new NotEmptyString('Hoeveel van hen behaalden 11/15 of meer?'),
            new PositiveNumber(11245)
        );
    }

    /**
     * @return Address
     */
    public static function createVsvAddress(): Address
    {
        return new Address(
            new NotEmptyString('Stationsstraat'),
            new NotEmptyString('110'),
            new NotEmptyString('2800'),
            new NotEmptyString('Mechelen')
        );
    }

    /**
     * @return Address
     */
    public static function createAwsrAddress(): Address
    {
        return new Address(
            new NotEmptyString('Avenue Comte de Smet de Nayer'),
            new NotEmptyString('14'),
            new NotEmptyString('5000'),
            new NotEmptyString('Namur')
        );
    }

    /**
     * @return ContestParticipant
     * @throws \Exception
     */
    public static function createContestParticipant(): ContestParticipant
    {
        return new ContestParticipant(
            new Email('par@ticipa.nt'),
            new NotEmptyString('Par'),
            new NotEmptyString('Ticipa'),
            new \DateTimeImmutable('1980-01-01T11:12:13+00:00')
        );
    }

    /**
     * @return ContestParticipation
     * @throws \Exception
     */
    public static function createQuizContestParticipation(): ContestParticipation
    {
        return new ContestParticipation(
            Uuid::fromString('c1eb30d1-990a-4a72-945f-190d00a26e9d'),
            new Year(2018),
            new Language(Language::NL),
            new QuizChannel(QuizChannel::INDIVIDUAL),
            ModelsFactory::createContestParticipant(),
            ModelsFactory::createVsvAddress(),
            new PositiveNumber(12345),
            new PositiveNumber(54321),
            true,
            true
        );
    }

    /**
     * @return ContestParticipation
     * @throws \Exception
     */
    public static function createCupContestParticipation(): ContestParticipation
    {
        return new ContestParticipation(
            Uuid::fromString('cb79548e-e856-4efa-a064-894c1c9b66fe'),
            new Year(2018),
            new Language(Language::NL),
            new QuizChannel(QuizChannel::CUP),
            ModelsFactory::createContestParticipant(),
            ModelsFactory::createVsvAddress(),
            new PositiveNumber(1234),
            new PositiveNumber(4321),
            true,
            true
        );
    }

    /**
     * @return EmployeeParticipation[]
     */
    public static function createEmployeeParticipations(): array
    {
        return [
            new EmployeeParticipation(
                Uuid::fromString('85fec50a-71ed-4d12-8a69-28a3cf5eb106'),
                new Email('jane@vsv.be')
            ),
            new EmployeeParticipation(
                Uuid::fromString('85fec50a-71ed-4d12-8a69-28a3cf5eb106'),
                new Email('jane@vsv.be')
            ),
            new EmployeeParticipation(
                Uuid::fromString('85fec50a-71ed-4d12-8a69-28a3cf5eb106'),
                new Email('elli@vsv.be')
            ),
            new EmployeeParticipation(
                Uuid::fromString('6e25425c-77cd-4899-9bfd-c2b8defb339f'),
                new Email('andy@awsr.be')
            ),
            new EmployeeParticipation(
                Uuid::fromString('6e25425c-77cd-4899-9bfd-c2b8defb339f'),
                new Email('john@awsr.be')
            ),
        ];
    }

    /**
     * @param Quiz $quiz
     * @param int $score
     * @return DomainMessage
     */
    public static function createQuizFinishedDomainMessage(
        Quiz $quiz,
        int $score = 10
    ): DomainMessage {
        return DomainMessage::recordNow(
            $quiz->getId(),
            0,
            new Metadata(),
            new QuizFinished(
                $quiz->getId(),
                $score
            )
        );
    }

    /**
     * @param Quiz $quiz
     * @param Question $question
     * @return DomainMessage
     * @throws \Exception
     */
    public static function createAnsweredCorrectDomainMessage(
        Quiz $quiz,
        Question $question
    ): DomainMessage {
        return DomainMessage::recordNow(
            $quiz->getId(),
            0,
            new Metadata(),
            new AnsweredCorrect(
                $quiz->getId(),
                $question,
                $question->getAnswers()->getCorrectAnswer(),
                new \DateTimeImmutable()
            )
        );
    }

    /**
     * @param Quiz $quiz
     * @param Question $question
     * @return DomainMessage
     * @throws \Exception
     */
    public static function createAnsweredInCorrectDomainMessage(
        Quiz $quiz,
        Question $question
    ): DomainMessage {
        return DomainMessage::recordNow(
            $quiz->getId(),
            0,
            new Metadata(),
            new AnsweredInCorrect(
                $quiz->getId(),
                $question,
                $question->getAnswers()->getCorrectAnswer(),
                new \DateTimeImmutable()
            )
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

    /**
     * @param string $model
     * @return string
     */
    public static function readCsv(string $model): string
    {
        return file_get_contents(__DIR__.'/Samples/'.$model.'.csv');
    }
}
