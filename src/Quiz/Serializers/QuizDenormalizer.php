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
     * @var QuestionDenormalizer
     */
    private $questionDenormalizer;

    /**
     * @param CompanyDenormalizer $companyDenormalizer
     * @param PartnerDenormalizer $partnerDenormalizer
     * @param QuestionDenormalizer $questionDenormalizer
     */
    public function __construct(
        CompanyDenormalizer $companyDenormalizer,
        PartnerDenormalizer $partnerDenormalizer,
        QuestionDenormalizer $questionDenormalizer
    ) {
        $this->companyDenormalizer = $companyDenormalizer;
        $this->partnerDenormalizer = $partnerDenormalizer;
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

        $company = $data['company'] !== '' ? $this->companyDenormalizer->denormalize(
            $data['company'],
            Company::class,
            $format,
            $context
        ) : null;

        $partner = $data['partner'] !== '' ? $this->partnerDenormalizer->denormalize(
            $data['partner'],
            Company::class,
            $format,
            $context
        ) : null;

        return new Quiz(
            Uuid::fromString($data['id']),
            new QuizParticipant(new Email($data['participant'])),
            new QuizChannel($data['channel']),
            $company,
            $partner,
            new Language($data['language']),
            new Year($data['year']),
            new AllowedDelay($data['allowedDelay']),
            new Questions(...$questions)
        );
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return ($type === Quiz::class) && ($format === 'json');
    }
}
