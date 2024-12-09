<?php

namespace App\Controller;

use App\Entity\Breed;
use App\Entity\Type;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
