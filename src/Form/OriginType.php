<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

class OriginType extends AbstractType
{

    final public function buildForm(FormBuilderInterface $builder, array $options): void
    {
            $builder
                ->add('origin', TextType::class)
                ->add('host', UrlType::class)
                ->add('submit', SubmitType::class);
    }
}