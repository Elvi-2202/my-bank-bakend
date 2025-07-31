<?php

namespace App\Controller;

use App\Entity\Operation;
use App\Repository\OperationRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/operations')]
class OperationController extends AbstractController
{
    private EntityManagerInterface $em;
    private OperationRepository $operationRepo;
    private CategoryRepository $categoryRepo;
    private UserRepository $userRepo;

    public function __construct(EntityManagerInterface $em, OperationRepository $operationRepo, CategoryRepository $categoryRepo, UserRepository $userRepo)
    {
        $this->em = $em;
        $this->operationRepo = $operationRepo;
        $this->categoryRepo = $categoryRepo;
        $this->userRepo = $userRepo;
    }

    #[Route('/', name: 'operation_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $operations = $this->operationRepo->findAll();
        $data = [];

        foreach ($operations as $operation) {
            $data[] = [
                'id' => $operation->getId(),
                'label' => $operation->getLabel(),
                'amount' => $operation->getAmount(),
                'date' => $operation->getDate()->format('Y-m-d'),
                'category' => [
                    'id' => $operation->getCategory()->getId(),
                    'title' => $operation->getCategory()->getTitle(),
                ],
                'user' => [
                    'id' => $operation->getUser()->getId(),
                    'username' => $operation->getUser()->getUsername(),
                ],
            ];
        }

        return $this->json($data, 200);
    }

    #[Route('/', name: 'operation_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['label']) || !isset($data['amount']) || empty($data['date']) || empty($data['category_id']) || empty($data['user_id'])) {
            return $this->json(['error' => 'Champs requis manquants'], 400);
        }

        $category = $this->categoryRepo->find($data['category_id']);
        if (!$category) {
            return $this->json(['error' => 'Catégorie non trouvée'], 404);
        }

        $user = $this->userRepo->find($data['user_id']);
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non trouvé'], 404);
        }

        $date = \DateTime::createFromFormat('Y-m-d', $data['date']);
        if (!$date) {
            return $this->json(['error' => 'Date invalide, format attendu YYYY-MM-DD'], 400);
        }

        $operation = new Operation();
        $operation->setLabel($data['label']);
        $operation->setAmount(floatval($data['amount']));
        $operation->setDate($date);
        $operation->setCategory($category);
        $operation->setUser($user);

        $this->em->persist($operation);
        $this->em->flush();

        return $this->json(['message' => 'Opération créée', 'operationId' => $operation->getId()], 201);
    }

    #[Route('/{id}', name: 'operation_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $operation = $this->operationRepo->find($id);
        if (!$operation) {
            return $this->json(['error' => 'Opération non trouvée'], 404);
        }

        $data = [
            'id' => $operation->getId(),
            'label' => $operation->getLabel(),
            'amount' => $operation->getAmount(),
            'date' => $operation->getDate()->format('Y-m-d'),
            'category' => [
                'id' => $operation->getCategory()->getId(),
                'title' => $operation->getCategory()->getTitle(),
            ],
            'user' => [
                'id' => $operation->getUser()->getId(),
                'username' => $operation->getUser()->getUsername(),
            ],
        ];

        return $this->json($data, 200);
    }
}
