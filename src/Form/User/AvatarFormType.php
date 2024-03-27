<?php

namespace App\Form\User;

use App\Form\BaseType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class AvatarFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->setMethod('POST');
        $builder->add('file', FileType::class, [
            'required' => true,
            'constraints' => [
                new NotBlank(),
                new File([
                    'maxSize' => '10M',
                    'mimeTypes' => [
                        'image/*',
                        'application/*',
                        'video/*',
                        'audio/*',
                        'text/*',
                        'message/*',
                        'archive/*',
                    ],
                    'mimeTypesMessage' => 'Please select valid file',
                    'maxSizeMessage' => 'Please select valid file with size up to 10MB',
                ]),
            ],
        ])
        ;
    }

}