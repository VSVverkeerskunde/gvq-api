<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Question\Models\Answer;
use VSV\GVQ_API\Question\Models\Question;

class QuestionNormalizer implements NormalizerInterface
{
    /**
     * @var CategoryNormalizer
     */
    private $categoryNormalizer;

    /**
     * @var AnswerNormalizer
     */
    private $answerNormalizer;

    /**
     * @param CategoryNormalizer $categoryNormalizer
     * @param AnswerNormalizer $answerNormalizer
     */
    public function __construct(
        CategoryNormalizer $categoryNormalizer,
        AnswerNormalizer $answerNormalizer
    ) {
        $this->categoryNormalizer = $categoryNormalizer;
        $this->answerNormalizer = $answerNormalizer;
    }

    /**
     * @inheritdoc
     * @param Question $question
     * @throws \Exception
     */
    public function normalize($question, $format = null, array $context = []): array
    {
        $category = $this->categoryNormalizer->normalize(
            $question->getCategory(),
            $format
        );

        $questionAsked = !empty($context['questionAsked']) && $context['questionAsked'] === true;

        $answers = array_map(
            function (Answer $answer) use ($format, $context) {
                return $this->answerNormalizer->normalize(
                    $answer,
                    $format,
                    $context
                );
            },
            $question->getAnswers()->toArray()
        );

        $archivedOn = $question->getArchivedOn() ? $question->getArchivedOn()->format(DATE_ATOM) : null;

        return [
            'id' => $question->getId()->toString(),
            'language' => $question->getLanguage()->toNative(),
            'year' => $question->getYear()->toNative(),
            'category' => $category,
            'text' => $question->getText()->toNative(),
            'imageFileName' => $question->getImageFileName()->toNative(),
            'answers' => $answers,
            'feedback' => $questionAsked ? null : $question->getFeedback()->toNative(),
            'createdOn' => $question->getCreatedOn()->format(DATE_ATOM),
            'archivedOn' => $archivedOn,
        ];
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return ($data instanceof Question) && ($format === 'json');
    }
}
