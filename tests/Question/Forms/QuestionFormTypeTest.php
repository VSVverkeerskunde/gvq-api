<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Forms;

use VSV\GVQ_API\Common\Forms\ExtensionsAwareTypeTestCase;
use VSV\GVQ_API\Common\ValueObjects\Languages;
use VSV\GVQ_API\Factory\ModelsFactory;
use VSV\GVQ_API\Question\Models\Categories;

class QuestionFormTypeTest extends ExtensionsAwareTypeTestCase
{
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
