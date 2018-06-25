<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Forms;

use VSV\GVQ_API\Common\Forms\ExtensionsAwareTypeTestCase;

class RequestPasswordResetFormTypeTest extends ExtensionsAwareTypeTestCase
{
    /**
     * @test
     */
    public function it_can_be_created(): void
    {
        $form = $this->factory->create(
            RequestPasswordResetFormType::class,
            null,
            [
                'translator' => $this->translator,
            ]
        );

        $form->submit([]);

        $this->assertTrue($form->isSynchronized());
    }
}
