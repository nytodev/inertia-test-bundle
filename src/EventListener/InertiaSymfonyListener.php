<?php

declare(strict_types=1);

namespace Nytodev\InertiaSymfony\EventListener;

use Nytodev\InertiaSymfony\Service\InertiaServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

final class InertiaSymfonyListener implements EventSubscriberInterface
{
    public function __construct(private readonly InertiaServiceInterface $inertiaService)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->headers->has('X-Inertia')) {
            return;
        }

        dump('Request', $request->headers->all());
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->headers->has('X-Inertia')) {
            return;
        }

        $response = $event->getResponse();
        $response->headers->set('Vary', 'X-Inertia');

        if ($request->getMethod() === Request::METHOD_GET && $request->headers->get('X-Inertia-Version') !== $this->inertiaService->getVersion()) {
            $response->headers->set('X-Inertia-Location', $request->getUri());
        }

        if ($response->isOk() && empty($response->getContent())) {
            $referer = $request->headers->get('referer');
            if ($referer) {
                $event->setResponse(new RedirectResponse($referer));
            } else {
                $event->setResponse(new RedirectResponse('/'));
            }
        }

        if ($response->getStatusCode() === Response::HTTP_FOUND && \in_array($request->getMethod(), [Request::METHOD_PUT, Request::METHOD_PATCH, Request::METHOD_DELETE], true)) {
            $response->setStatusCode(Response::HTTP_SEE_OTHER);
        }

        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
            ResponseEvent::class => 'onKernelResponse',
        ];
    }
}
