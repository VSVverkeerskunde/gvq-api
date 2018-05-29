<?php declare(strict_types=1);

namespace VSV\GVQ_API\Image\Validation;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UploadFileValidator
{
    /**
     * @param UploadedFile $uploadedFile
     * @throws \InvalidArgumentException
     */
    public function validate(UploadedFile $uploadedFile): void;
}
