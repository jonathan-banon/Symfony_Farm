<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Type;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/type')]
final class TypeController extends AbstractController
{
    #[Route('/all', name: 'ajax_all_types')]
    public function fetchTypes(TypeRepository $typeRepository): Response
    {
        $types = $typeRepository->findAll();
        $data = [];

        foreach ($types as $type) {
            $data[] = ['id' => $type->getId(), 'name' => $type->getName()];
        }
        return new JsonResponse($data);
    }

    #[Route('/{id}/breeds', name: 'ajax_breeds_by_type', methods: ['GET'])]
    public function breedsByType(int $id, TypeRepository $typeRepository): Response
    {
        $type = $typeRepository->find($id);

        if (!$type) {
            return new JsonResponse(['error' => 'Type introuvable'], Response::HTTP_NOT_FOUND);
        }

        $breeds = $type->getBreeds();
        foreach ($breeds as $breed) {
            $data[] = [
                'id' => $breed->getId(),
                'name' => $breed->getName(),
            ];
        }

        return new JsonResponse($data);
    }



    #[Route('/new', name: 'app_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent(), true);
            $type = new Type();
            $type->setName($data['name']);

            $entityManager->persist($type);
            $entityManager->flush();

            return new Response(null, Response::HTTP_OK);
        }
        return new Response('Données invalides', Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}/del', name: 'app_type_delete', methods: ['POST'])]
    public function delete(Type $type, EntityManagerInterface $entityManager, TypeRepository $typeRepository): Response
    {
        $filesystem = new Filesystem();
        foreach ($type->getAnimals() as $animal) {
            $animalId = $animal->getId();
            $uploadDir = __DIR__ . '/../../public/images/animal-' . $animalId;

            if ($filesystem->exists($uploadDir)) {
                try {
                    $filesystem->remove($uploadDir);
                } catch (\Exception $e) {
                    return new JsonResponse([
                        'error' => 'Erreur lors de la suppression des fichiers de l\'animal ' . $animalId . ': ' . $e->getMessage()
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        }

        try {
            $entityManager->remove($type);
            $entityManager->flush();
            $newType = $entityManager->getRepository(Type::class)->findOneBy([], ['id' => 'ASC']);
            return new JsonResponse([
                'message' => 'Type supprimé avec succès !',
                'newTypeId' => $newType ? $newType->getId() : null
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la suppression du type : ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/edit', name: 'app_type_edit', methods: ['POST'])]
    public function edit(Request $request, Type $type, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent(), true);

            $type->setName($data['name']);
            $entityManager->flush();

            return new Response(null, Response::HTTP_OK);
        }
        return new Response('Données invalides', Response::HTTP_BAD_REQUEST);
    }


}
