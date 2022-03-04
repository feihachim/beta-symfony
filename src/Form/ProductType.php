<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('brochure', FileType::class, [
                'label' => 'Brochure (Fichier image)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Choisissez une image valide'
                    ])
                ],
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie du produit',
                'class' => Category::class,
                'choice_label' => 'title',
                'placeholder' => 'Choisissez une catégorie',
                'query_builder' => function (CategoryRepository $categoryRepo) {
                    return $categoryRepo->createQueryBuilder('c')->orderBy('c.title', 'ASC');
                },
                'constraints' => new NotBlank(['message' => 'Choisissez une catégorie please!'])
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
