<?php

namespace App\Controller;

use App\Entity\Breed;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/breed')]
final class BreedController extends AbstractController
{
    #[Route('/{id}/add', name: 'add_breed', methods: ['POST'])]
    public function addBreedByType(int $id, TypeRepository $typeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $type = $typeRepository->find($id);

        if (!$type) {
            return new JsonResponse(['error' => 'Type introuvable'], Response::HTTP_NOT_FOUND);
        }
        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent(), true);
            $breed = new Breed();
            $breed->setName($data['name']);
            $breed->setType($type);

            $entityManager->persist($breed);
            $entityManager->flush();

            return new Response(null, Response::HTTP_OK);
        }
        return new Response('Données invalides', Response::HTTP_BAD_REQUEST);
    }
}
