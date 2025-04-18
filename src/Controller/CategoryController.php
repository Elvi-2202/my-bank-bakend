<?php

// src/Controller/CategoryController.php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/categories", name="category_index", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->json($categories);
    }

    /**
     * @Route("/api/category", name="category_create", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        // Create a new Category
        $category = new Category();
        $category->setTitle($data['title']);
        $category->setDescription($data['description']);

        // Persist Category entity
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $this->json(['message' => 'Category created successfully!'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/category/{id}", name="category_show", methods={"GET"})
     */
    public function show(Category $category): Response
    {
        return $this->json($category);
    }

    /**
     * @Route("/api/category/{id}", name="category_update", methods={"PUT"})
     */
    public function update(Request $request, Category $category): Response
    {
        $data = json_decode($request->getContent(), true);

        $category->setTitle($data['title']);
        $category->setDescription($data['description']);

        // Update Category entity
        $this->entityManager->flush();

        return $this->json(['message' => 'Category updated successfully!']);
    }

    /**
     * @Route("/api/category/{id}", name="category_delete", methods={"DELETE"})
     */
    public function delete(Category $category): Response
    {
        // Remove Category entity
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return $this->json(['message' => 'Category deleted successfully!']);
    }
}
