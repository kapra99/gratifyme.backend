<?php

namespace App\Form;

use App\EventSubscriber\FormSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Range;

abstract class BaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new FormSubscriber());
    }

    public function addPaginationParameters(FormBuilderInterface $builder)
    {
        $builder
            ->add('page', IntegerType::class, [
                "required" => false,
                "empty_data" => 1,
                'constraints' => [
                    new Positive(),
                ]
            ])
            ->add('itemsPerPage', IntegerType::class, [
                "required" => false,
                "empty_data" => 25,
                'constraints' => [
                    new Positive(),
                    new Range([
                        "min" => 5,
                        "max" => 50
                    ])
                ]
            ]);
    }

    public function addDateFilterParameters(FormBuilderInterface $builder)
    {
        $builder
            ->add('fromDate', DateTimeType::class, [
                "required" => false,
            ])
            ->add('toDate', DateTimeType::class, [
                "required" => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // $resolver->setDefaults([
        //     // Configure your form options here
        // ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
