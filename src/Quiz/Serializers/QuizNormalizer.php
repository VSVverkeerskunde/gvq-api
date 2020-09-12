<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Company\Serializers\CompanyNormalizer;
use VSV\GVQ_API\Partner\Serializers\PartnerNormalizer;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Serializers\QuestionNormalizer;
use VSV\GVQ_API\Quiz\Models\Quiz;
use VSV\GVQ_API\Team\Serializers\TeamNormalizer;

class QuizNormalizer implements NormalizerInterface
{
    /**
     * @var CompanyNormalizer
     */
    private $companyNormalizer;

    /**
     * @var PartnerNormalizer
     */
    private $partnerNormalizer;

    /**
     * @var TeamNormalizer
     */
    private $teamNormalizer;

    /**
     * @var QuestionNormalizer
     */
    private $questionNormalizer;

    /**
     * @param CompanyNormalizer $companyNormalizer
     * @param PartnerNormalizer $partnerNormalizer
     * @param TeamNormalizer $teamNormalizer
     * @param QuestionNormalizer $questionNormalizer
     */
    public function __construct(
        CompanyNormalizer $companyNormalizer,
        PartnerNormalizer $partnerNormalizer,
        TeamNormalizer $teamNormalizer,
        QuestionNormalizer $questionNormalizer
    ) {
        $this->companyNormalizer = $companyNormalizer;
        $this->partnerNormalizer = $partnerNormalizer;
        $this->teamNormalizer = $teamNormalizer;
        $this->questionNormalizer = $questionNormalizer;
    }

    /**
     * @inheritdoc
     * @param Quiz $quiz
     */
    public function normalize($quiz, $format = null, array $context = []): array
    {
        $questions = array_map(
            function (Question $question) use ($format) {
                return $this->questionNormalizer->normalize(
                    $question,
                    $format
                );
            },
            $quiz->getQuestions()->toArray()
        );

        $company = $quiz->getCompany() ? $this->companyNormalizer->normalize(
            $quiz->getCompany(),
            $format,
            $context
        ) : null;

        $partner = $quiz->getPartner() ? $this->partnerNormalizer->normalize(
            $quiz->getPartner(),
            $format,
            $context
        ) : null;

        $team = $quiz->getTeam() ? $this->teamNormalizer->normalize(
            $quiz->getTeam(),
            $format,
            $context
        ) : null;

        return [
            'id' => $quiz->getId()->toString(),
            'participant' => $quiz->getParticipant() ? $quiz->getParticipant()->getEmail()->toNative(): null,
            'channel' => $quiz->getChannel()->toNative(),
            'company' => $company,
            'partner' => $partner,
            'team' => $team,
            'language' => $quiz->getLanguage()->toNative(),
            'year' => $quiz->getYear()->toNative(),
            'allowedDelay' => $quiz->getAllowedDelay()->toNative(),
            'questions' => $questions,
            'score' => $quiz->getScore(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof Quiz) && ($format === 'json');
    }
}
