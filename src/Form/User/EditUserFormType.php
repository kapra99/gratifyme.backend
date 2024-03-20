<?php

namespace App\Form\User;


use App\Form\BaseType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditUserFormType extends BaseType
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
            ]);
//            ->add('avatarImagePath', FileType::class, [
//                'required' => false,
//            ]);
    }

}
