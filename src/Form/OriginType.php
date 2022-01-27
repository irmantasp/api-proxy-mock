<?php

namespace App\Form;

use App\Entity\Origin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
            ->add('record', CheckboxType::class, [
                'label' => 'Should response be recorded and used as mock?',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ],
                'label_attr' => [
                    'class' => 'form-check-label'
                ],
            ])
            ->add('saveOriginalRequest', CheckboxType::class, [
                'label' => 'Should request be recorded along with mock?',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ],
                'label_attr' => [
                    'class' => 'form-check-label'
                ],
            ])
            ->add('submit', SubmitType::class);
    }
}