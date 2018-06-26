<?php declare(strict_types=1);

namespace VSV\GVQ_API\Image\Controllers;

use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use VSV\GVQ_API\Image\Validation\UploadFileValidator;

class ImageControllerTest extends TestCase
{
    /**
     * @var Filesystem|MockObject
     */
    private $fileSystem;

    /**
     * @var UploadFileValidator|MockObject
     */
    private $imageValidator;

    /**
     * @var UuidFactoryInterface|MockObject
     */
    private $uuidFactory;

    /**
     * @var ImageController
     */
    private $imageController;

    protected function setUp(): void
    {
        /** @var Filesystem|MockObject $fileSystem */
        $fileSystem = $this->createMock(Filesystem::class);
        $this->fileSystem = $fileSystem;

        /** @var UploadFileValidator|MockObject $imageValidator */
        $imageValidator = $this->createMock(UploadFileValidator::class);
        $this->imageValidator = $imageValidator;

        /** @var UuidFactoryInterface|MockObject $uuidFactory */
        $uuidFactory = $this->createMock(UuidFactoryInterface::class);
        $this->uuidFactory = $uuidFactory;

        $this->imageController = new ImageController(
            $this->fileSystem,
            $this->imageValidator,
            $this->uuidFactory
        );
    }

    /**
     * @test
     * @throws FileExistsException
     */
    public function it_can_handle_a_file_upload(): void
    {
        $uuid = Uuid::uuid4();
        $this->uuidFactory->expects($this->once())
            ->method('uuid4')
            ->willReturn($uuid);

        /** @var UploadedFile|MockObject $uploadFile */
        $uploadFile = $this->createMock(UploadedFile::class);

        $uploadFile->expects($this->once())
            ->method('getRealPath')
            ->willReturn(__FILE__);

        $uploadFile->expects($this->once())
            ->method('getClientOriginalExtension')
            ->willReturn('jpg');

        $this->fileSystem->expects($this->once())
            ->method('writeStream');

        $request = new Request(
            [],
            [],
            [],
            [],
            [
                'image' => $uploadFile,
            ]
        );

        $actualResponse = $this->imageController->upload($request);

        $this->assertEquals(
            '{"filename":"'.$uuid->toString().'.jpg"}',
            $actualResponse->getContent()
        );
        $this->assertEquals(
            'application/json',
            $actualResponse->headers->get('Content-Type')
        );
    }

    /**
     * @test
     * @throws FileExistsException
     */
    public function it_requires_exactly_one_file(): void
    {
        $request = new Request();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Exactly one image is required.');

        $this->imageController->upload($request);
    }

    /**
     * @test
     * @throws FileExistsException
     */
    public function it_requires_image_key(): void
    {
        $request = new Request(
            [],
            [],
            [],
            [],
            [
                'not_image' => $this->createMock(UploadedFile::class),
            ]
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Key image is required inside form-data.');

        $this->imageController->upload($request);
    }

    /**
     * @test
     * @throws FileNotFoundException
     */
    public function it_deletes_the_previous_image_if_it_exists(): void
    {
        $fileName = 'existingImage.jpg';

        $this->fileSystem
            ->expects($this->once())
            ->method('has')
            ->with($fileName)
            ->willReturn(true);

        $this->fileSystem
            ->expects($this->once())
            ->method('delete')
            ->with($fileName);

        $this->imageController->delete($fileName);
    }

    /**
     * @test
     * @throws FileNotFoundException
     */
    public function it_does_not_try_to_delete_nonexisting_image(): void
    {
        $fileName = 'existingImage.jpg';

        $this->fileSystem
            ->expects($this->once())
            ->method('has')
            ->with($fileName)
            ->willReturn(false);

        $this->fileSystem
            ->expects($this->never())
            ->method('delete')
            ->with($fileName);

        $this->imageController->delete($fileName);
    }
}
