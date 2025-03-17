<?php
declare(strict_types=1);

namespace Nytodev\InertiaSymfony\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class InertiaSymfonyListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->headers->has('X-Inertia')) {
            dump('Inertia request');
            return;
        }

        dd('Inertia request a');
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->headers->has('X-Inertia')) {
            dump('Inertia response');
            return;
        }

        dd('Inertia response a');
    }
}
