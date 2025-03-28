<?php

declare(strict_types=1);

namespace Nytodev\InertiaSymfony\EventListener;

use Nytodev\InertiaSymfony\Service\InertiaHeader;
use Nytodev\InertiaSymfony\Service\InertiaServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class InertiaSymfonyListener implements EventSubscriberInterface
{
    public function __construct(private readonly InertiaServiceInterface $inertiaService)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->headers->has(InertiaHeader::XInertia->value)) {
            return;
        }
        // Errors are not handled here, they are handled in the InertiaService

    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->headers->has(InertiaHeader::XInertia->value)) {
            return;
        }

        $response = $event->getResponse();
        $response->headers->set('Vary', InertiaHeader::XInertia->value);

        if ($request->getMethod() === Request::METHOD_GET && $request->headers->get(InertiaHeader::XInertiaVersion->value) !== $this->inertiaService->getVersion()) {
            $event->setResponse($this->inertiaService->getLocation($request->getUri()));
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
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}
