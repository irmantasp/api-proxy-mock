<?php

namespace App\Form\Extension\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class TransformOptionsType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('hashUri', CheckboxType::class, [
            'label' => 'Hash URL for filename generation?',
            'required' => false,
            'attr' => [
                'class' => 'form-check-input'
            ],
            'label_attr' => [
                'class' => 'form-check-label'
            ],
        ]);
    }
}