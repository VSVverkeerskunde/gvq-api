<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\Forms;

use VSV\GVQ_API\Common\Forms\ExtensionsAwareTypeTestCase;
use VSV\GVQ_API\Common\ValueObjects\Languages;
use VSV\GVQ_API\User\ValueObjects\Role;
use VSV\GVQ_API\User\ValueObjects\Roles;

class UserFormTypeTest extends ExtensionsAwareTypeTestCase
{
    /**
     * @test
     */
    public function it_can_be_created(): void
    {
        $form = $this->factory->create(
            UserFormType::class,
            null,
            [
                'languages' => new Languages(),
                'roles' => new Roles(
                    new Role('contact'),
                    new Role('vsv'),
                    new Role('admin')
                ),
                'user' => null,
                'translator' => $this->translator,
            ]
        );

        $form->submit([]);

        $this->assertTrue($form->isSynchronized());
    }
}
