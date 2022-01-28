<?php

namespace App\Form\Extension\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HeaderType extends AbstractType
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
        return 'headers_collection';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('header', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Header',
                ],
            ])
            ->add('value', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Value',
                ],
            ])
            ->add('remove', ButtonType::class, [
                'label' => '-',
                'attr' => [
                    'class' => 'btn btn-outline-primary header_entry--remove',
                    'onclick' => 'this.closest(".headers_entry").remove()'
                ],
            ]);
    }
}