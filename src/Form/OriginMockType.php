<?php

namespace App\Form;

use App\Entity\Mock;
use App\Entity\Origin;
use App\Form\Extension\Type\HeaderType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OriginMockType extends AbstractType
{
    final public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('originId', HiddenType::class)
            ->add('uri', TextType::class)
            ->add('method', ChoiceType::class, [
                'choices' => [
                    Request::METHOD_CONNECT,
                    Request::METHOD_DELETE,
                    Request::METHOD_GET,
                    Request::METHOD_HEAD,
                    Request::METHOD_OPTIONS,
                    Request::METHOD_PATCH,
                    Request::METHOD_POST,
                    Request::METHOD_PURGE,
                    Request::METHOD_PUT,
                    Request::METHOD_TRACE
                ],
                'choice_label' => static function ($choice, $key, $value) {
                    return $value;
                }
            ])
            ->add('status', ChoiceType::class, [
                'choices' => array_flip(Response::$statusTexts),
                'choice_label' => static function ($choice, $key, $value) {
                    return sprintf('%s (%s)', $value, $key);
                }
            ])
            ->add('headers', CollectionType::class, [
                'entry_type' => HeaderType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'prototype'    => true,
                'attr'         => [
                    'class' => "headers-collection",
                ],
                'entry_options' => [
                    'attr' => [
                        'class' => 'input-group mb-3'
                    ]
                ]
            ])
            ->add('content', TextareaType::class, [
                'required' => false
            ])
            ->add('lock', CheckboxType::class, [
                'label' => 'Protect from overwriting',
                'help' => 'Do not overwrite mock record file, even if proxy settings allow overriding.',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ],
                'label_attr' => [
                    'class' => 'form-check-label'
                ],
                'help_attr' => [
                    'class' => 'form-text'
                ],
            ])
            ->add('filePath', TextType::class, [
                'disabled' => !$this->isNew($options),
            ])
            ->add('submit', SubmitType::class);
    }

    private function isNew(array $options = []): bool {
        if (!isset($options['data'])) {
            return true;
        }

        $data = $options['data'];

        if ($data instanceof Mock) {
            return is_null($data->getFilePath());
        }

        return true;
    }
}