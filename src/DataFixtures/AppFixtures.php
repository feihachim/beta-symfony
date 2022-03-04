<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Service\FileUploader;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        for ($i = 1; $i <= 3; $i++) {
            $category = new Category();
            $category->setTitle("Catégorie n°$i");
            $category->setContent("Description numéro $i");

            $manager->persist($category);

            for ($j = 1; $j <= 2; $j++) {
                $product = new Product();
                $product->setTitle("Produit numéro $j");

                $product->setBrochureFilename("exo-foot-mcd-621f7ce63cc29.png");
                $product->setCategory($category);

                $manager->persist($product);
            }
        }

        $manager->flush();
    }
}
