<?php

namespace App\Controller;

use App\Repository\AnimalRepository;
use App\Repository\TypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(TypeRepository $typeRepository): Response
    {
        $types = $typeRepository->findAll();
        $data = []; 
        
        foreach ($types as $type) {
            $data[] = ['id' => $type->getId(), 'name' => $type->getName()];
        }

        return $this->render('home/index.html.twig', [
            'types' => $data, 
        ]);
    }

    #[Route('/types/animals/{id}', name: 'ajax_animals_by_type', methods: ['GET'])]
    public function animalsByType(int $id, AnimalRepository $animalRepository)
    {
        $animals = $animalRepository->findByTypeId($id);
        $data = [];
        foreach ($animals as $animal) {
            $data[] = ['id' => $animal->getId(), 'name' => $animal->getName(), 'breed' => $animal->getBreed()->getName(), 'description' => $animal->getDescription()];
        }
        return new JsonResponse($data);
    }
}
