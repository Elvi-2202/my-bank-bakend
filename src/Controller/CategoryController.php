<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/categories')]
class CategoryController extends AbstractController
{
    private EntityManagerInterface $em;
    private CategoryRepository $categoryRepo;

    public function __construct(EntityManagerInterface $em, CategoryRepository $categoryRepo)
    {
        $this->em = $em;
        $this->categoryRepo = $categoryRepo;
    }

    #[Route('/', name: 'category_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $categories = $this->categoryRepo->findAll();
        $data = [];

        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->getId(),
                'title' => $category->getTitle(),
                'description' => $category->getDescription(),
            ];
        }

        return $this->json($data, 200);
    }

    #[Route('/', name: 'category_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['title']) || empty($data['description'])) {
            return $this->json(['error' => 'Champs requis manquants'], 400);
        }

        $category = new Category();
        $category->setTitle($data['title']);
        $category->setDescription($data['description']);

        $this->em->persist($category);
        $this->em->flush();

        return $this->json(['message' => 'Catégorie créée', 'categoryId' => $category->getId()], 201);
    }

    #[Route('/{id}', name: 'category_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryRepo->find($id);

        if (!$category) {
            return $this->json(['error' => 'Catégorie non trouvée'], 404);
        }

        $data = [
            'id' => $category->getId(),
            'title' => $category->getTitle(),
            'description' => $category->getDescription(),
        ];

        return $this->json($data, 200);
    }
}
