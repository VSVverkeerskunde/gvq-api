<?php declare(strict_types=1);

namespace VSV\GVQ_API\Image\Controllers;

use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Image\Validation\UploadFileValidator;

class ImageController
{
    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var UploadFileValidator
     */
    private $imageValidator;

    /**
     * @var UuidFactoryInterface
     */
    private $uuidFactory;

    /**
     * @param Filesystem $fileSystem
     * @param UploadFileValidator $imageValidator
     * @param UuidFactoryInterface $uuidFactory
     */
    public function __construct(
        Filesystem $fileSystem,
        UploadFileValidator $imageValidator,
        UuidFactoryInterface $uuidFactory
    ) {
        $this->fileSystem = $fileSystem;
        $this->imageValidator = $imageValidator;
        $this->uuidFactory = $uuidFactory;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws FileExistsException
     */
    public function upload(Request $request): Response
    {
        $uploadFile = $this->guardFiles($request->files);

        $filename = $this->handleImage($uploadFile);

        $response = new Response('{"filename":"'.$filename->toNative().'"}');
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return NotEmptyString
     * @throws FileExistsException
     */
    public function handleImage(UploadedFile $uploadedFile): NotEmptyString
    {
        $this->imageValidator->validate($uploadedFile);

        $uuid = $this->uuidFactory->uuid4();
        $filename = $uuid->toString().'.'.$uploadedFile->getClientOriginalExtension();

        $stream = fopen($uploadedFile->getRealPath(), 'r+');
        $this->fileSystem->writeStream($filename, $stream);
        fclose($stream);

        return new NotEmptyString($filename);
    }

    /**
     * @param string $fileName
     * @return bool
     * @throws FileNotFoundException
     */
    public function delete(string $fileName): bool
    {
        if ($this->fileSystem->has($fileName)) {
            return $this->fileSystem->delete($fileName);
        }

        return false;
    }

    /**
     * @param FileBag $files
     * @return UploadedFile
     */
    private function guardFiles(FileBag $files): UploadedFile
    {
        if (count($files) !== 1) {
            throw new \InvalidArgumentException('Exactly one image is required.');
        }

        if (empty($files->get('image'))) {
            throw new \InvalidArgumentException('Key image is required inside form-data.');
        }

        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $files->get('image');

        $this->imageValidator->validate($uploadedFile);

        return $uploadedFile;
    }
}
