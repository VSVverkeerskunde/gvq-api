<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Forms;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Translation\TranslatorInterface;
use VSV\GVQ_API\Common\Forms\ExtensionsTrait;
use VSV\GVQ_API\Common\ValueObjects\Languages;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Models\Categories;

class QuestionFormTypeTest extends TypeTestCase
{
    use ExtensionsTrait;

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
}
