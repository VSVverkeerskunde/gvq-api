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
     */
    public function normalize($question, $format = null, array $context = []): array
    {
        $category = $this->categoryNormalizer->normalize(
            $question->getCategory(),
            $format
        );

        $answers = array_map(
            function (Answer $answer) use ($format) {
                return $this->answerNormalizer->normalize(
                    $answer,
                    $format
                );
            },
            $question->getAnswers()->toArray()
        );

        /** @var Question $question */
        return [
            'id' => $question->getId()->toString(),
            'language' => $question->getLanguage()->toNative(),
            'year' => $question->getYear()->toNative(),
            'category' => $category,
            'questionText' => $question->getQuestionText()->toNative(),
            'pictureUri' => $question->getPictureUri()->__toString(),
            'answers' => $answers,
            'feedback' => $question->getFeedback()->toNative(),
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
