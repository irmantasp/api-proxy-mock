<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class AdminRouteEventSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['checkAdminRouteAccess', 0]
            ]
        ];
    }

    final public function checkAdminRouteAccess(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if ($_ENV['APP_ADMIN_UI'] !== '1') {
            throw new AccessDeniedHttpException('Admin UI is disabled.');
        }
    }
}