<?php

namespace App\Form\Extension\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RedirectParamsType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    final public function getBlockPrefix(): string
    {
        return 'redirect_params';
    }

    /**
     * {@inheritdoc}
     */
    final public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('max', NumberType::class, [
                'label' => 'Maximum number of allowed redirects',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 5,
                ],
            ])
            ->add('strict', CheckboxType::class, [
                'label' => 'Check to use strict redirects.',
                'help' => 'Strict RFC compliant redirects mean that POST redirect requests are sent as POST requests vs. doing what most browsers do which is redirect POST requests with GET requests.',
                'help_attr' => [
                    'class' => 'form-text',
                ],
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ],
                'label_attr' => [
                    'class' => 'form-check-label'
                ],
            ])
            ->add('referer', CheckboxType::class, [
                'label' => 'Check to enable adding the Referer header when redirecting.',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ],
                'label_attr' => [
                    'class' => 'form-check-label'
                ],
            ])
            ->add('protocols', ChoiceType::class, [
                'label' => 'Which protocols are allowed for redirect requests.',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'choices' => [
                    'HTTP' => 'http',
                    'HTTPS' => 'https',
                ],
                'attr' => [
                    'class' => 'form-group mb-3'
                ],
                'choice_attr' => [
                    'class' => 'form-check-input',
                ]
            ])
            ->add('track_redirects', CheckboxType::class, [
                'label' => 'All URIs and status codes will be stored in the order which the redirects were encountered.',
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