<?php declare(strict_types=1);

namespace VSV\GVQ_API\Common\Forms;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\CoreExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class ExtensionsAwareTypeTestCase extends TypeTestCase
{
    /**
     * @var TranslatorInterface|MockObject
     */
    protected $translator;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var TranslatorInterface|MockObject $translator */
        $translator = $this->createMock(TranslatorInterface::class);
        $this->translator = $translator;
    }

    /**
     * @return array
     */
    protected function getExtensions(): array
    {
        $extensions = parent::getExtensions();

        /** @var ValidatorInterface|MockObject $validator */
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
