<?php
declare(strict_types=1);

namespace Nytodev\InertiaSymfony\Twig;

use Nytodev\InertiaSymfony\Service\InertiaService;
use Twig\Extension\RuntimeExtensionInterface;
use Twig\Markup;

final class InertiaSymfonyRuntime implements RuntimeExtensionInterface
{
    public function __construct(private InertiaService $inertiaService)
    {
    }

    public function render($page): Markup
    {
        return new Markup(sprintf(
            '<div id="app" data-page="%s"></div>',
            htmlspecialchars(json_encode($page))
        ), 'UTF-8');
    }

    public function head(): Markup
    {
        return new Markup('', 'UTF-8');
    }
}
