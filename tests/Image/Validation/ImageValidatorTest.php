<?php declare(strict_types=1);

namespace VSV\GVQ_API\Image\Validation;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageValidatorTest extends TestCase
{
    /** @var UploadedFile|MockObject */
    private $uploadedFile;

    /** @var ImageValidator */
    private $imageValidator;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        /** @var UploadedFile|MockObject $uploadFile */
        $uploadFile = $this->createMock(UploadedFile::class);
        $this->uploadedFile = $uploadFile;

        $this->imageValidator = new ImageValidator(
            2 * 1024 * 1024,
            [
                'image/jpeg',
                'image/png'
            ]
        );
    }

    /**
     * @test
     */
    public function it_throws_when_upload_file_is_invalid()
    {
        $this->uploadedFile->expects($this->once())
            ->method('isValid')
            ->willReturn(false);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Image was not uploaded successful.');

        $this->imageValidator->validate($this->uploadedFile);
    }

    /**
     * @test
     */
    public function it_throws_when_upload_file_size_is_too_big()
    {
        $this->uploadedFile->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->uploadedFile->expects($this->once())
            ->method('getClientSize')
            ->willReturn(3 * 1024 * 1024);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File is bigger then 2 MB.');

        $this->imageValidator->validate($this->uploadedFile);
    }

    /**
     * @test
     */
    public function it_throws_when_upload_file_has_unsupported_mime_type()
    {
        $this->uploadedFile->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->uploadedFile->expects($this->once())
            ->method('getClientSize')
            ->willReturn(1.5 * 1024 * 1024);

        $this->uploadedFile->expects($this->once())
            ->method('getMimeType')
            ->willReturn('image/gif');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Only image types image/jpeg, image/png are supported.'
        );

        $this->imageValidator->validate($this->uploadedFile);
    }
}
