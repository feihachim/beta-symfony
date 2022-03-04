<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\FileUploader;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class HomeController extends AbstractController
{

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ObjectManager
     */
    private $entityManager;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->productRepository = $doctrine->getRepository(Product::class);
        $this->entityManager = $doctrine->getManager();
    }

    /**
     * @Route("/",name="app_home")
     *
     * @return Response
     */
    public function home(): Response
    {
        return $this->render('home.html.twig');
    }

    /**
     * @Route("/products", name="app_product_list")
     */
    public function index(): Response
    {
        $products = $this->productRepository->findAll();
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'products' => $products
        ]);
    }

    /**
     * @Route("/products/edit/{id}",name="app_product_edit",requirements={"id"="\d+"})
     * @Route("/products/new", name="app_product_new")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param FileUploader $fileUploader
     * @param integer $id
     * @return Response
     */
    public function edit(Request $request, FileUploader $fileUploader, int $id = null): Response
    {
        if ($id === null) {
            $product = new Product();
        } else {
            $product = $this->productRepository->find($id);
        }
        $fileUploader->setTargetDirectory('uploads/brochures/');
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * @var UploadedFile
             */
            $brochureFile = $form->get('brochure')->getData();

            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $product->setBrochureFilename($brochureFileName);
            }
            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_product_list');
        }

        return $this->renderForm('home/new.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * @Route("/products/{id}", name="app_product_show", requirements={"id"="\d+"})
     */
    public function show(int $id): Response
    {
        $product = $this->productRepository->find($id);
        return $this->render('home/show.html.twig', [
            'product' => $product
        ]);
    }
}
