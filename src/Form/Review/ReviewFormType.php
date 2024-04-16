<?php

namespace App\Form\Review;

use App\Form\BaseType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReviewFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message', TextType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(max: 255),
                ],
            ])
            ->add('rating', NumberType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(max: 255),
                ],
            ])
            ->add('userId', TextType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(max: 255),
                ],
            ])
            ->add('author', TextType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(max: 255),
                ],
            ]);
    }

}