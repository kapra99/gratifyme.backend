<?php

namespace App\Form\File;

use App\Form\BaseType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddFileType extends BaseType
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
                        'maxSize' => '20M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Моля изберете валидна снимка',
                        'maxSizeMessage' => 'Моля изберете снимка с максимален размер 20MB',
                    ]),
                ],
            ])
        ;
    }
}
