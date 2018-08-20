<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Forms;

use VSV\GVQ_API\Common\Forms\ExtensionsAwareTypeTestCase;

class EditContactFormTypeTest extends ExtensionsAwareTypeTestCase
{
    /**
     * @test
     */
    public function it_can_be_created(): void
    {
        $form = $this->factory->create(
            EditContactFormType::class,
            null,
            [
                'user' => null,
                'translator' => $this->translator,
            ]
        );

        $form->submit([]);

        $this->assertTrue($form->isSynchronized());
    }
}
