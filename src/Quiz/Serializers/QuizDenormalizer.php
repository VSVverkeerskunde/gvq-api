<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Company\Serializers\CompanyDenormalizer;
use VSV\GVQ_API\Partner\Serializers\PartnerDenormalizer;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Models\Questions;
use VSV\GVQ_API\Question\Serializers\QuestionDenormalizer;
use VSV\GVQ_API\Question\ValueObjects\Year;
use VSV\GVQ_API\Quiz\Models\Quiz;
use VSV\GVQ_API\Quiz\ValueObjects\AllowedDelay;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Quiz\ValueObjects\QuizParticipant;
use VSV\GVQ_API\Team\Models\Team;
use VSV\GVQ_API\Team\Serializers\TeamDenormalizer;
use VSV\GVQ_API\User\ValueObjects\Email;

class QuizDenormalizer implements DenormalizerInterface
{
    /**
     * @var CompanyDenormalizer
     */
    private $companyDenormalizer;

    /**
     * @var PartnerDenormalizer
     */
    private $partnerDenormalizer;

    /**
     * @var TeamDenormalizer
     */
    private $teamDenormalizer;

    /**
     * @var QuestionDenormalizer
     */
    private $questionDenormalizer;

    /**
     * @param CompanyDenormalizer $companyDenormalizer
     * @param PartnerDenormalizer $partnerDenormalizer
     * @param TeamDenormalizer $teamDenormalizer
     * @param QuestionDenormalizer $questionDenormalizer
     */
    public function __construct(
        CompanyDenormalizer $companyDenormalizer,
        PartnerDenormalizer $partnerDenormalizer,
        TeamDenormalizer $teamDenormalizer,
        QuestionDenormalizer $questionDenormalizer
    ) {
        $this->companyDenormalizer = $companyDenormalizer;
        $this->partnerDenormalizer = $partnerDenormalizer;
        $this->teamDenormalizer = $teamDenormalizer;
        $this->questionDenormalizer = $questionDenormalizer;
    }

    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = []): Quiz
    {
        $questions = array_map(
            function (array $question) use ($format, $context) {
                return $this->questionDenormalizer->denormalize(
                    $question,
                    Question::class,
                    $format,
                    $context
                );
            },
            $data['questions']
        );

        $company = isset($data['company']) ? $this->companyDenormalizer->denormalize(
            $data['company'],
            Company::class,
            $format,
            $context
        ) : null;

        $partner = isset($data['partner']) ? $this->partnerDenormalizer->denormalize(
            $data['partner'],
            Company::class,
            $format,
            $context
        ) : null;

        $team = isset($data['team']) ? $this->teamDenormalizer->denormalize(
            $data['team'],
            Team::class,
            $format,
            $context
        ) : null;

        $quiz = new Quiz(
            Uuid::fromString($data['id']),
            new QuizChannel($data['channel']),
            $company,
            $partner,
            $team,
            new Language($data['language']),
            new Year($data['year']),
            new AllowedDelay($data['allowedDelay']),
            new Questions(...$questions)
        );

        if (isset($data['participant'])) {
            $quiz = $quiz->withParticipant(new QuizParticipant(new Email($data['participant'])));
        }

        if (isset($data['score']) && $data['score'] > 0) {
            $quiz = $quiz->withScore($data['score']);
        }

        return $quiz;
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return ($type === Quiz::class) && ($format === 'json');
    }
}
