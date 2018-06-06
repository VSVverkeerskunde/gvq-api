<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Forms;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\CoreExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use VSV\GVQ_API\Common\ValueObjects\Languages;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Models\Categories;

class QuestionFormTypeTest extends TypeTestCase
{
    /**
     * @var TranslatorInterface|MockObject
     */
    private $translator;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @var TranslatorInterface|MockObject $translator */
        $translator = $this->createMock(TranslatorInterface::class);
        $this->translator = $translator;
    }

    /**
     * @test
     */
    public function it_can_be_created(): void
    {
        $form = $this->factory->create(
            QuestionFormType::class,
            null,
            [
                'categories' => new Categories(
                    ModelsFactory::createAccidentCategory(),
                    ModelsFactory::createGeneralCategory()
                ),
                'languages' => new Languages(),
                'question' => null,
                'translator' => $this->translator,
            ]
        );

        $form->submit([]);

        $this->assertTrue($form->isSynchronized());
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    protected function getExtensions(): array
    {
        $extensions = parent::getExtensions();

        $validator = $this->createMock(ValidatorInterface::class);

        $validator
            ->method('validate')
            ->will($this->returnValue(new ConstraintViolationList()));
        $validator
            ->method('getMetadataFor')
            ->will($this->returnValue(new ClassMetadata(Form::class)));

        $extensions[] = new ValidatorExtension($validator);
        $extensions[] = new CoreExtension();

        return $extensions;
    }
}
