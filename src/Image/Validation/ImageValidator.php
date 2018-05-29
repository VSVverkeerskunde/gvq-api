<?php declare(strict_types=1);

namespace VSV\GVQ_API\Image\Validation;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageValidator implements UploadFileValidator
{
    /** @var int */
    private $maxFileSize;

    /** @var string[] */
    private $supportedMimeTypes;

    /**
     * @param int $maxFileSize in bytes
     * @param string[] $supportedMimeTypes
     */
    public function __construct(int $maxFileSize, array $supportedMimeTypes)
    {
        $this->maxFileSize = $maxFileSize;
        $this->supportedMimeTypes = $supportedMimeTypes;
    }

    /**
     * @inheritdoc
     */
    public function validate(UploadedFile $uploadedFile): void
    {
        if (!$uploadedFile->isValid()) {
            throw new \InvalidArgumentException(
                'Image was not uploaded successful.'
            );
        }

        $bytes = $uploadedFile->getClientSize();
        if ($bytes > $this->maxFileSize) {
            $mb = $this->maxFileSize / (1024 * 1024);
            throw new \InvalidArgumentException('File is bigger then '.$mb.' MB.');
        }

        if (!in_array($uploadedFile->getMimeType(), $this->supportedMimeTypes)) {
            $types = implode(', ', $this->supportedMimeTypes);
            throw new \InvalidArgumentException('Only image types '.$types.' are supported.');
        }
    }
}
