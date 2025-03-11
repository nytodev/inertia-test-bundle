<?php

declare(strict_types=1);

namespace Nytodev\InertiaSymfony\Twig;

use Twig\Extension\RuntimeExtensionInterface;
use Twig\Markup;

final class InertiaSymfonyRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
    }

    /**
     * @param array<string, mixed> $page
     */
    public function render(array $page): Markup
    {
        return new Markup(\sprintf(
            '<div id="app" data-page="%s"></div>',
            htmlspecialchars(json_encode($page) ?: '{}', \ENT_QUOTES, 'UTF-8')
        ), 'UTF-8');
    }

    public function head(): Markup
    {
        return new Markup('', 'UTF-8');
    }
}
