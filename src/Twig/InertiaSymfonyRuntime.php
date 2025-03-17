<?php

declare(strict_types=1);

namespace Nytodev\InertiaSymfony\Twig;

use Nytodev\InertiaSymfony\Ssr\GatewayInterface;
use Nytodev\InertiaSymfony\Ssr\Response;
use Twig\Extension\RuntimeExtensionInterface;
use Twig\Markup;

final class InertiaSymfonyRuntime implements RuntimeExtensionInterface
{
    private bool $isSsrInertiaDispatched = false;
    private ?Response $ssrResponse = null;
    public function __construct(private readonly GatewayInterface $gateway)
    {
    }

    /**
     * @param array<string, mixed> $page
     */
    public function render(array $page): Markup
    {
        if (!$this->isSsrInertiaDispatched) {
            $this->ssrResponse = $this->gateway->dispatch($page);
            $this->isSsrInertiaDispatched = true;
        }

        if ($this->ssrResponse) {
            return new Markup($this->ssrResponse->body, 'UTF-8');
        }

        return new Markup(\sprintf(
            '<div id="app" data-page="%s"></div>',
            htmlspecialchars(json_encode($page) ?: '{}', \ENT_QUOTES, 'UTF-8')
        ), 'UTF-8');
    }

    /**
     * @param array<string, mixed> $page
     */
    public function head(array $page): Markup
    {
        if (!$this->isSsrInertiaDispatched) {
            $this->ssrResponse = $this->gateway->dispatch($page);
            $this->isSsrInertiaDispatched = true;
        }

        if ($this->ssrResponse) {
            return new Markup($this->ssrResponse->head, 'UTF-8');
        }

        return new Markup('', 'UTF-8');
    }
}
