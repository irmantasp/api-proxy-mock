<?php

namespace App\Form;

use App\Entity\Origin;
use App\Form\Extension\Type\IgnoreContentType;
use App\Form\Extension\Type\IgnoreHeaderType;
use App\Form\Extension\Type\RedirectParamsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
            ->add('proxyConfig', CheckboxType::class, [
                'label' => 'Override global proxy configuration?',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ],
                'label_attr' => [
                    'class' => 'form-check-label'
                ],
            ])
            ->add('proxyVerify', CheckboxType::class, [
                'label' => 'Verify SSL certificate',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ],
                'label_attr' => [
                    'class' => 'form-check-label'
                ],
            ])
            ->add('proxyAllowRedirect', CheckboxType::class, [
                'label' => 'Allow request redirects',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ],
                'label_attr' => [
                    'class' => 'form-check-label'
                ],
            ])
            ->add('proxyRedirectParams', RedirectParamsType::class, [
                'label' => 'Redirect parameters',
            ])
            ->add('proxyDebug', CheckboxType::class, [
                'label' => 'Debug',
                'help' => "Set to true or set to a PHP stream returned by fopen() to enable debug output with the handler used to send a request. For example, when using cURL to transfer requests, cURL's verbose of CURLOPT_VERBOSE will be emitted. When using the PHP stream wrapper, stream wrapper notifications will be emitted. If set to true, the output is written to PHP's STDOUT. If a PHP stream is provided, output is written to the stream.",
                'help_attr' => [
                    'class' => 'form-text',
                ],
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ],
                'label_attr' => [
                    'class' => 'form-check-label'
                ],
            ])
            ->add('proxyErrors', CheckboxType::class, [
                'label' => 'Throw exceptions on an HTTP protocol errors',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ],
                'label_attr' => [
                    'class' => 'form-check-label'
                ],
            ])
            ->add('proxyTimeout', NumberType::class, [
                'label' => 'Float describing the total timeout of the request in seconds.',
                'help' => ' Use 0 to wait indefinitely (the default behavior).',
                'help_attr' => [
                    'class' => 'form-text',
                ],
                'required' => false,
                'attr' => [
                    'min' => 0,
                    'step' => 1,
                ],
                'label_attr' => [
                    'class' => ''
                ],
            ])
            ->add('proxyVersion', NumberType::class, [
                'label' => 'Protocol version to use with the request.',
                'required' => false,
                'attr' => [
                    'min' => 1.0,
                    'step' => 0.1,
                ],
                'label_attr' => [
                    'class' => ''
                ],
            ])
            ->add('submit', SubmitType::class);

//private array $proxyRedirectParams = [];

    }
}