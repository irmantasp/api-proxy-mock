<?php

namespace App\Form;

use App\Entity\Origin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

class OriginType extends AbstractType
{

    private function isNew(array $options = []): bool {
        if (!isset($options['data'])) {
            return true;
        }

        $data = $options['data'];

        if ($data instanceof Origin) {
            return $data->isNew();
        }

        return true;
    }
    final public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['disabled' => !$this->isNew($options)])
            ->add('label', TextType::class)
            ->add('host', UrlType::class)
            ->add('mode', ChoiceType::class, ['expanded' => true, 'choices' => ['mock' => 'Mock', 'proxy' => 'Proxy']])
            ->add('submit', SubmitType::class);
    }
}