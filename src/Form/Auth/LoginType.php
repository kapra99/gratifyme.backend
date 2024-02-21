<?php

namespace App\Form\Auth;

use App\Form\BaseType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

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
}