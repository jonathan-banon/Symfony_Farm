<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['_username']) || !isset($data['_password'])) {
            return new Response('Paramètres manquants', 400);
        }

        $username = $data['_username'];
        $password = $data['_password'];

        $user = $userRepository->findOneBy(['email' => $username]);

        if ($user && $passwordHasher->isPasswordValid($user, $password)) {
            $session = $request->getSession();
            $session->set('user', $user);
            return $this->json([
                'message' => 'Connexion réussie!',
                'redirect' => $this->generateUrl('app_home')
            ], 200);
        }

        return $this->json([
            'message' => 'Email ou mot de passe incorrect'
        ], 401);
    }

    #[Route('/disconnect', name: 'disconnect', methods: ['POST'])]
    public function logout(Request $request): Response
    {
        $session = $request->getSession();
        $session->remove('user');

        return $this->json([
            'message' => 'Déconnexion réussie!',
            'session' => $session
        ], 200);
    }
   
}
