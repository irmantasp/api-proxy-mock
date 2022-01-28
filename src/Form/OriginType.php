<?php

namespace App\Form;

use App\Entity\Origin;
use App\Form\Extension\Type\IgnoreContentType;
use App\Form\Extension\Type\IgnoreHeaderType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OriginType extends AbstractType
{
    private const IGNORE_HEADERS_DEFAULT = [
        'connection',
        'date',
        'cookie',
        'user-agent',
        'upgrade-insecure-requests',
        'referer',
        'host',
        'cache-control',
        'pragma',
        'sec-ch-ua',
        'sec-ch-ua-mobile',
        'sec-ch-ua-platform',
        'sec-fetch-site',
        'sec-fetch-mode',
        'sec-fetch-user',
        'sec-fetch-dest',
    ];

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
        /** @var Origin $origin */
        $origin = $builder->getData();
        $ignoreHeaders = $this->isNew($options) ? static::IGNORE_HEADERS_DEFAULT : $origin->getIgnoreHeaders();
        $ignoreHeadersData = [];
        foreach ($ignoreHeaders as $ignoredHeader) {
            $ignoreHeadersData[] = ['value' => $ignoredHeader];
        }

        $ignoreContentData = [];
        foreach ($origin->getIgnoreContent() as $ignoreContent) {
            $ignoreContentData[] = ['value' => $ignoreContent];
        }

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
            ->add('log', CheckboxType::class, [
                'label' => 'Should original request and response be written to logs?',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ],
                'label_attr' => [
                    'class' => 'form-check-label'
                ],
            ])
            ->add('ignoreHeaders', CollectionType::class, [
                'entry_type' => IgnoreHeaderType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'prototype'    => true,
                'data' => $ignoreHeadersData,
                'attr'         => [
                    'class' => "ignore-headers-collection",
                ],
                'entry_options' => [
                    'attr' => [
                        'class' => 'input-group mb-3'
                    ]
                ]
            ])
            ->add('ignoreContent', CollectionType::class, [
                'entry_type' => IgnoreContentType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'prototype'    => true,
                'data' => $ignoreContentData,
                'attr'         => [
                    'class' => "ignore-content-collection",
                ],
                'entry_options' => [
                    'attr' => [
                        'class' => 'input-group mb-3'
                    ]
                ]
            ])
            ->add('ignoreFiles', CheckboxType::class, [
                'label' => 'Ignore file attachments',
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