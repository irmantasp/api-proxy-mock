<?php

namespace App\Form\Extension\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IgnoreContentType extends AbstractType
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
        return 'ignore_content_collection';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'someProperty.someChildProperty or attribute.childAttribute.key',
                ],
                'row_attr' => [
                    'class' => 'col'
                ]
            ])
            ->add('remove', ButtonType::class, [
                'label' => '-',
                'attr' => [
                    'class' => 'btn btn-outline-primary ignore_content_entry--remove',
                    'onclick' => 'this.closest(".ignore_content_entry").remove()'
                ],
                'row_attr' => [
                    'class' => 'col'
                ]
            ]);
    }
}