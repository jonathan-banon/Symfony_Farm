<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Breed;
use App\Entity\Photo;
use App\Entity\Type;
use App\Repository\AnimalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/animal')]
final class AnimalController extends AbstractController
{

    #[Route('/new', name: 'app_animal_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent(), true);
            $animal = new Animal();
            $type = $entityManager->getRepository(Type::class)->findOneBy(['id' => $data['type']]);
            $animal->setType($type);
            $animal->setName($data['name']);
            $breed = $entityManager->getRepository(Breed::class)->findOneBy(['id' => $data['breed']]);
            $animal->setBreed($breed);
            $animal->setAge($data['age']);
            $animal->setDescription($data['description']);
            $animal->setPrice($data['price']);
            $animal->setOnSale(true);

            $entityManager->persist($animal);
            $entityManager->flush();

            return new Response(null, Response::HTTP_OK);
        }
        return new Response('Données invalides', Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}/edit', name: 'app_animal_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Animal $animal, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent(), true);

            $animal->setName($data['name']);
            $breed = $entityManager->getRepository(Breed::class)->findOneBy(['name' => $data['breed']]);
            if ($breed) {
                $animal->setBreed($breed);
            } else {
                $this->addFlash('error', 'Race non trouvée');
            }
            $animal->setDescription($data['description']);
            $animal->setPrice($data['price']);
            $animal->setOnSale($data['status']);

            $entityManager->flush();

            return new Response(null, Response::HTTP_OK);
        }
        return new Response('Données invalides', Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}/del', name: 'app_animal_delete', methods: ['POST'])]
    public function delete(Request $request, Animal $animal, EntityManagerInterface $entityManager): Response
    {
        $filesystem = new Filesystem();
        $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/images/animal-' . $animal->getId();

        if ($request->isMethod('POST')) {
            $entityManager->remove($animal);
            $entityManager->flush();

            if ($filesystem->exists($uploadsDir)) {
                $filesystem->remove($uploadsDir);
            }

            return new Response(null, Response::HTTP_OK);
        }

        return new Response('Données invalides', Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}/delete-image/{imageIndex}', name: 'app_animal_delete_image', methods: ['POST'])]
    public function deleteImage(
        int $id,
        int $imageIndex,
        AnimalRepository $animalRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {

        $animal = $animalRepository->find($id);
        if (!$animal) {
            return new JsonResponse(['error' => 'Animal non trouvé'], 404);
        }

        $images = $animal->getPictures();
        if (!isset($images[$imageIndex])) {
            return new JsonResponse(['error' => 'Image non trouvée'], 404);
        }

        $imageToDelete = $images[$imageIndex];
        $filename = $imageToDelete->getFilename();

        $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/images/animal-' . $animal->getId();
        $filePath = $uploadsDir . '/' . $filename;

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $animal->removePicture($imageToDelete);

        $entityManager->remove($imageToDelete);
        $entityManager->flush(); 

        $remainingFiles = glob($uploadsDir . '/*'); 
        if (count($remainingFiles) === 0) {
            rmdir($uploadsDir);
        }

        return new JsonResponse(['message' => 'Image supprimée avec succès'], 200);
    }

    #[Route('/{id}/upload-image', name: 'animal_upload_image', methods: ['POST'])]
    public function uploadImage(Request $request, Animal $animal, EntityManagerInterface $entityManager): JsonResponse
    {
        $file = $request->files->get('image');
        if (!$file) {
            return $this->json(['error' => 'Aucun fichier fourni'], 400);
        }

        $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/images/animal-' . $animal->getId();
        $filesystem = new Filesystem();

        if (!$filesystem->exists($uploadsDir)) {
            $filesystem->mkdir($uploadsDir, 0755);
        }

        $filesInDir = glob($uploadsDir . '/img-*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        $nextImageIndex = count($filesInDir) + 1;
        $fileName = 'img-' . $nextImageIndex . '.' . $file->guessExtension();

        try {
            $file->move($uploadsDir, $fileName);
        } catch (FileException $e) {
            return $this->json(['error' => 'Erreur lors du téléchargement : ' . $e->getMessage()], 500);
        }


        $image = new Photo();
        $image->setFileName($fileName);
        $animal->addPicture($image);

        $entityManager->persist($image);
        $entityManager->flush();

        return $this->json([
            'message' => 'Image téléchargée avec succès',
            'imagePath' => '/images/animal-' . $animal->getId() . '/' . $fileName,
        ]);
    }
}
