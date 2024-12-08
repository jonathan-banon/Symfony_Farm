<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Breed;
use App\Entity\Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        if ($request->isMethod('POST')) {
            $entityManager->remove($animal);
            $entityManager->flush();

            return new Response(null, Response::HTTP_OK);
        }

        return new Response('Données invalides', Response::HTTP_BAD_REQUEST);
    }
}
