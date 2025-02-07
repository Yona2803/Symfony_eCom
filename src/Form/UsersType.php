<?php

namespace App\Form;

// use App\Entity\Carts;
use App\Entity\Users;
// use App\Entity\WishList;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\NotBlank;


class UsersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstName', TextType::class, [
            'label' => 'First Name',
            'attr' => [
                'placeholder' => 'Enter Your First Name',
            ],
        ])
        ->add('lastName', TextType::class, [
            'label' => 'Last Name',
            'attr' => [
                'placeholder' => 'Enter Your Last Name',
            ],
        ])
        ->add('email', EmailType::class, [
            'label' => 'Email Address',
            'attr' => [
                'placeholder' => 'Enter Your Email Address',
            ],
        ])
        ->add('address', TextType::class, [
            'label' => 'Address',
            'attr' => [
                'placeholder' => 'Enter Your Address',
            ],
        ])
        ->add('phoneNumber', TelType::class, [
            'label' => 'Phone Number',
            'attr' => [
                'placeholder' => 'Enter Your Phone Number',
            ],
        ])
        ->add('username', TextType::class, [
            'label' => 'User Name',
            'attr' => [
                'placeholder' => 'Enter Your User Name',
            ],
        ])
        ->add('currentPassword', PasswordType::class, [
           'mapped' => false,  // Not mapped to entity, only used for verification
            'required' => true,
            'label' => 'Current Password',
            'attr' => [
                    'autocomplete' => 'new-password',                     
                'placeholder' => 'Current Password',
            ],
        ])
        ->add('newPassword', PasswordType::class, [
            'mapped' => false, // Not mapped to entity
            'required' => false,
            'label' =>'Password Changes', 
            'attr' => [
                'placeholder' => 'New Password',
                'onchange' => 'checkPassWord("NewPass")',
            ],
        ])
        ->add('confirmPassword', PasswordType::class, [
            'mapped' => false, // Not mapped to entity
            'required' => false,
            'attr' => [
                'placeholder' => 'Confirm Password',
                'onchange' => 'checkPassWord("ConfirmPass")',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
