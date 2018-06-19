<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Question\Models\Question;

class ImageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TranslatorInterface $translator */
        $translator = $options['translator'];

        $builder
            ->add(
                'image',
                FileType::class,
                [
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => $translator->trans('Empty field'),
                            ]
                        ),

                    ],
                ]
            );
    }

    /**
     * @param Question $question
     * @param NotEmptyString $imageFileName
     * @return Question
     */
    public function updateQuestionImage(
        Question $question,
        NotEmptyString $imageFileName
    ): Question {
        return new Question(
            $question->getId(),
            $question->getLanguage(),
            $question->getYear(),
            $question->getCategory(),
            $question->getText(),
            $imageFileName,
            $question->getAnswers(),
            $question->getFeedback()
        );
    }
}
