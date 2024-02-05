<?php

namespace App\Form\User;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(max: 255),
                ],
            ])
//            ->add('password', PasswordType::class, [
//                'required' => true,
//                'trim' => true,
//                'constraints' => [
//                    new NotBlank(),
//                    new Length(null, 6, 255),
//                ],
//            ])
            ->add('firstname', TextType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(null, 2, 255),
                ],
            ])
            ->add('surname', TextType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(null, 2, 255),
                ],
            ])
            ->add('lastname', TextType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(null, 2, 255),
                ],
            ])
            ->add('nickname', TextType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(null, 2, 255),
                ],
            ])
            ->add('dateofbirth', TextType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(null, 2, 255),
                ],
            ])
            ->add('workplace', TextType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new Length(null, 2, 255),
                ],
            ])
            ->add('workingposition', TextType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new Length(null, 2, 255),
                ],
            ])
            ->add('tipmethod', TextType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new Length(null, 2, 255),
                ],
            ]);
    }

//    public function configureOptions(OptionsResolver $resolver)
//    {
//        $resolver->setDefaults([
//            'data_class' => EditUsersDto::class,
//        ]);
//    }
}
