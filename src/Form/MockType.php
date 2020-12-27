<?php

namespace App\Form;

use App\Manager\OriginManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
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
        $builder
            ->add('originId', ChoiceType::class, [
                'choices' => $this->originManager->loadMultiple(),
                'choice_label' => static function ($choice, $key, $label) {
                    return $choice->getLabel();
                },
                'choice_value' => 'label'
            ])
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
                    return $value;
                }
            ])
            ->add('headers', CollectionType::class, [
                'entry_type' => TextType::class,
            ])
            ->add('content', TextareaType::class)
            ->add('submit', SubmitType::class);
    }
}