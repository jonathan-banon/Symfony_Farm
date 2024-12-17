<?php

namespace App\Controller;

use App\Repository\AnimalRepository;
use App\Repository\TypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(TypeRepository $typeRepository, Request $request): Response
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

    #[Route('/check-session', name: 'app_check_session', methods: ['GET'])]
    public function checkSession(Request $request): Response
    {
        $session = $request->getSession();
        $user = $session->get('user');
        return $this->json([
            'isLoggedIn' => $user ? true : false,
            'user' => $user
        ]);
    }

    #[Route('/type/{id}/animals/', name: 'ajax_animals_by_type', methods: ['GET'])]
    public function animalsByType(int $id, AnimalRepository $animalRepository): Response
    {
        $animals = $animalRepository->findByTypeId($id);
        $data = [];
        foreach ($animals as $animal) {
            $firstImage = $animal->getPictures()->first();
            $imagePath = $firstImage ? '/images/animal-' . $animal->getId() . '/' . $firstImage->getFilename() : null;

            $data[] = [
                'id' => $animal->getId(),
                'name' => $animal->getName(),
                'breed' => $animal->getBreed()->getName(),
                'description' => $animal->getDescription(),
                'price' => $animal->getPrice(),
                'isOnSale' => $animal->isOnSale() ? 'true' : 'false',
                'previewImage' => $imagePath,
            ];
        }
        return new JsonResponse($data);
    }
}
