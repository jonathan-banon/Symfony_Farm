<?php

namespace App\Controller;


use App\Repository\TypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/type')]
final class TypeController extends AbstractController
{
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
}
