<?php

namespace App\Form;

use App\Entity\Mock;
use App\Entity\Origin;
use App\Form\Extension\Type\HeaderType;
use App\Manager\OriginManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MockType extends AbstractType
{
    private $originManager;

    public function __construct(OriginManagerInterface $originManager)
    {
        $this->originManager = $originManager;
    }

    final public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $origins = $this->originManager->loadMultiple();
        $origins_options = [];
        foreach ($origins as $origin) {
            $origins_options[$origin->getLabel()] = $origin->getName();
        }

        $builder
            ->add('originId', ChoiceType::class, [
                'choices' => $origins_options,
            ])
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
                },
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