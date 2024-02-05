<?php

namespace App\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->setMethod('POST');

        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(max: 255),
                ],
            ])
            ->add('password', PasswordType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(null, 6, 255),
                ],
            ]);
    }
//    public function configureOptions(OptionsResolver $resolver)
//    {
//        $resolver->setDefaults([
//            'data_class' => RegisterUsersDto::class,
//        ]);
//    }
}
