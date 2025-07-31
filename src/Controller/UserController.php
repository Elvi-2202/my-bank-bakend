<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/api/users')]
class UserController extends AbstractController
{
    private EntityManagerInterface $em;
    private UserRepository $userRepo;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepo, UserPasswordHasherInterface $passwordHasher)
    {
        $this->em = $em;
        $this->userRepo = $userRepo;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/register', name: 'user_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            return $this->json(['error' => 'Champs requis manquants'], 400);
        }

        if ($this->userRepo->findOneBy(['email' => $data['email']]) || $this->userRepo->findOneBy(['username' => $data['username']])) {
            return $this->json(['error' => 'Utilisateur déjà existant'], 409);
        }

        $user = new User();
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $this->em->persist($user);
        $this->em->flush();

        return $this->json(['message' => 'Utilisateur créé', 'userId' => $user->getId()], 201);
    }

    #[Route('/login', name: 'user_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['username']) || empty($data['password'])) {
            return $this->json(['error' => 'Champs requis manquants'], 400);
        }

        $user = $this->userRepo->findOneBy(['username' => $data['username']]) ?? $this->userRepo->findOneBy(['email' => $data['username']]);

        if (!$user) {
            return $this->json(['error' => 'Utilisateur non trouvé'], 404);
        }

        if (!$this->passwordHasher->isPasswordValid($user, $data['password'])) {
            return $this->json(['error' => 'Mot de passe incorrect'], 401);
        }

        return $this->json(['message' => 'Connexion réussie', 'userId' => $user->getId()], 200);
    }

    #[Route('/', name: 'user_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $users = $this->userRepo->findAll();
        $data = [];

        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
            ];
        }

        return $this->json($data, 200);
    }
}
