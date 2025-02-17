<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Items;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'required' => true,
            ])
            ->add('price', NumberType::class, [
                'scale' => 2,
                'label' => 'Price',
                'required' => true,
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Stock',
                'required' => true,
                'attr' => [
                    'min' => 1,
                    'value' => 1
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'name',
                'label' => 'Category',
                'placeholder' => 'Choose a category',
                'required' => true,
            ])


            ->add('tags', ChoiceType::class, [
                'label' => 'Tags',
                'required' => true,
                'choices' => [
                    "Woman's Fashion" => "Woman's Fashion",
                    "Men's Fashion" => "Men's Fashion",
                    "Electronics" => "Electronics",
                    "Home & Lifestyle" => "Home & Lifestyle",
                    "Medicine" => "Medicine",
                    "Sport & Outdoor" => "Sport & Outdoor",
                    "Baby's & Toys" => "Baby's & Toys",
                    "Groceries & Pets" => "Groceries & Pets",
                    "Health & Beauty" => "Health & Beauty",
                ],
                'multiple' => true,
                'expanded' => true,
                'attr' => [
                    'class' => 'inline-checkbox-group'
                ],
            ])


            ->add('description', TextareaType::class)
            ->add('itemImage', FileType::class, [
                'mapped' => false,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Items::class,
        ]);
    }
}
