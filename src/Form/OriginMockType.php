<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OriginMockType extends AbstractType
{
    final public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('originId', HiddenType::class)
            ->add('uri', UrlType::class)
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
                'choices' => Response::$statusTexts,
                'choice_label' => static function ($choice, $key, $value) {
                    return sprintf('%s (%s)', $value, $key);
                }
            ])
            ->add('headers', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('content', TextareaType::class)
            ->add('submit', SubmitType::class);
    }
}