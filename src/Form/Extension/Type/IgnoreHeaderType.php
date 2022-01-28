<?php

namespace App\Form\Extension\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IgnoreHeaderType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'compound' => true,
            ''
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ignore_headers_collection';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Header',
                ],
            ])
            ->add('remove', ButtonType::class, [
                'label' => '-',
                'attr' => [
                    'class' => 'btn btn-outline-primary ignore_header_entry--remove',
                    'onclick' => 'this.closest(".ignore_headers_entry").remove()'
                ],
            ]);
    }
}