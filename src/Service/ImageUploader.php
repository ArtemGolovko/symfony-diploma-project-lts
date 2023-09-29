<?php

namespace App\Service;

use Easybook\Slugger;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploader
{
    /**
     * @var Slugger
     */
    private Slugger $slugger;

    /**
     * @var FilesystemOperator
     */
    private FilesystemOperator $filesystem;

    /**
     * @param FilesystemOperator $filesystem
     */
    public function __construct(FilesystemOperator $filesystem)
    {
        $this->slugger = new Slugger();
        $this->filesystem = $filesystem;
    }

    /**
     * @param UploadedFile $file
     *
     * @return string
     * @throws FilesystemException
     */
    public function upload(UploadedFile $file): string
    {
        $clientFilename = $file->getClientOriginalName();

        $filename = $this->slugger->uniqueSlugify(pathinfo($clientFilename, PATHINFO_FILENAME), '-')
            . '.' . $file->guessExtension();

        $stream = fopen($file->getPathname(), 'r');
        $this->filesystem->writeStream($filename, $stream);

        if (\is_resource($stream)) {
            fclose($stream);
        }

        return $filename;
    }
}
