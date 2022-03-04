<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @var CategoryRepository
     */

    private $categoryRepository;

    /**
     * @var ObjectManager
     */
    private $entityManager;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->categoryRepository = $doctrine->getRepository(Category::class);
        $this->entityManager = $doctrine->getManager();
    }

    /**
     * @Route("/category", name="app_category")
     */
    public function index(): Response
    {
        $categories = $this->categoryRepository->findAll();
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/category/{id}", name="app_category_show", requirements={"id"="\d+"})
     * @param integer $id
     */
    public function show(int $id): Response
    {
        $category = $this->categoryRepository->find($id);

        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @Route("/category/edit/{id}", name="app_category_edit", requirements={"id"="\d+"})
     * @Route("/category/new", name="app_category_new")
     * @param integer $id
     * @return Response
     */
    public function formCategory(Request $request, int $id = null): Response
    {
        if ($id === null) {
            $category = new Category();
            $editMode = false;
        } else {
            $category = $this->categoryRepository->find($id);
            $editMode = true;
        }
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_category');
        }
        return $this->renderForm('category/new.html.twig', [
            'form' => $form,
            'editMode' => $editMode
        ]);
    }
}
