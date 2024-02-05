<?php

namespace App\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username',EmailType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(max: 255),
                ],
            ])
            ->add('password',PasswordType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(null, 6, 255),
                ],
            ]);
//        ->add('submit', SubmitType::class);

    }

//    public function configureOptions(OptionsResolver $resolver)
//    {
//        $resolver->setDefaults([
//            'data_class' => LoginUsersDto::class,
//        ]);
//    }
}
