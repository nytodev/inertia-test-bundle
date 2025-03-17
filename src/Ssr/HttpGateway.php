<?php

declare(strict_types=1);

namespace Nytodev\InertiaSymfony\Ssr;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class HttpGateway implements GatewayInterface
{
    public function __construct(private readonly ContainerInterface $container, private readonly HttpClientInterface $client)
    {
    }

    public function dispatch(array $page): ?Response
    {
        if (!$this->container->hasParameter('inertia_symfony.ssr.enabled') || !$this->container->getParameter('inertia_symfony.ssr.enabled')) {
            return null;
        }

        $url = $this->container->getParameter('inertia_symfony.ssr.url') ?? 'http://localhost:13714/render';

        if (!\is_string($url) || !filter_var($url, \FILTER_VALIDATE_URL)) {
            return null;
        }

        try {
            $response = $this->client->request(
                Request::METHOD_POST,
                $url,
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'body' => json_encode($page),
                ]
            );
            $content = $response->toArray();
        } catch (\Exception|TransportExceptionInterface|DecodingExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            return null;
        }

        return new Response(implode("\n", $content['head']), $content['body']);
    }
}
