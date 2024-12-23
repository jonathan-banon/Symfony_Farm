<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploaderService
{
    private string $projectDir;
    private Filesystem $filesystem;

    public function __construct(string $projectDir, Filesystem $filesystem)
    {
        $this->projectDir = $projectDir;
        $this->filesystem = $filesystem;
    }

    public function uploadAnimalImage(UploadedFile $file, int $animalId): string
    {
        if (!$file) {
            throw new \InvalidArgumentException('Aucun fichier fourni');
        }

        $uploadsDir = $this->projectDir . '/public/images/animal-' . $animalId;

        if (!$this->filesystem->exists($uploadsDir)) {
            $this->filesystem->mkdir($uploadsDir, 0755);
        }

        $filesInDir = glob($uploadsDir . '/img-*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        $nextImageIndex = count($filesInDir) + 1;
        $fileName = 'img-' . $nextImageIndex . '.' . $file->guessExtension();

        try {
            $file->move($uploadsDir, $fileName);
        } catch (FileException $e) {
            throw new \RuntimeException('Erreur lors du téléchargement : ' . $e->getMessage());
        }

        return '/images/animal-' . $animalId . '/' . $fileName;
    }
}
