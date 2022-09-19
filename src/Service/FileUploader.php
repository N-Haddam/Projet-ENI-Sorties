<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{

    public function __construct(private $targetDirectory, private SluggerInterface $slugger){}

    public function upload(UploadedFile $file): string {
        $originalImageName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeImageName = $this->slugger->slug($originalImageName);
        $newImageName = $safeImageName.'-'.uniqid().'.'.$file->guessExtension();
        $file->move(
            $this->getTargetDirectory(), $newImageName );
        return $newImageName;
    }

    public function getTargetDirectory() {
        return $this->targetDirectory;
    }

}