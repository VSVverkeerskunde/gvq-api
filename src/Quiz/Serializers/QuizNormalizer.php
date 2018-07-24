<?php declare(strict_types=1);

namespace VSV\GVQ_API\Quiz\Serializers;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Question\Models\Question;
use VSV\GVQ_API\Question\Serializers\QuestionNormalizer;
use VSV\GVQ_API\Quiz\Models\Quiz;

class QuizNormalizer implements NormalizerInterface
{
    /**
     * @var QuestionNormalizer
     */
    private $questionNormalizer;

    /**
     * @param QuestionNormalizer $questionNormalizer
     */
    public function __construct(QuestionNormalizer $questionNormalizer)
    {
        $this->questionNormalizer = $questionNormalizer;
    }

    /**
     * @inheritdoc
     * @param Quiz $quiz
     */
    public function normalize($quiz, $format = null, array $context = array()): array
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

        return [
            'id' => $quiz->getId()->toString(),
            'participant' => $quiz->getParticipant()->getEmail()->toNative(),
            'type' => $quiz->getType()->toNative(),
            'channel' => $quiz->getChannel()->toNative(),
            'language' => $quiz->getLanguage()->toNative(),
            'year' => $quiz->getYear()->toNative(),
            'questions' => $questions,
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
