<?php declare(strict_types=1);

namespace VSV\GVQ_API\Image\Controllers;

use League\Flysystem\Filesystem;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @throws \League\Flysystem\FileExistsException
     */
    public function upload(Request $request): Response
    {
        $uploadFile = $this->guardFiles($request->files);

        $uuid = $this->uuidFactory->uuid4();
        $filename = $uuid->toString().'.'. $uploadFile->getClientOriginalExtension();

        $stream = fopen($uploadFile->getRealPath(), 'r+');
        $this->fileSystem->writeStream($filename, $stream);
        fclose($stream);

        $response = new Response('{"filename":"'.$filename.'"}');
        $response->headers->set('Content-Type', 'application/json');

        return $response;
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

        /** @var UploadedFile $uploadFile */
        $uploadFile = $files->get('image');

        $this->imageValidator->validate($uploadFile);

        return $uploadFile;
    }
}
