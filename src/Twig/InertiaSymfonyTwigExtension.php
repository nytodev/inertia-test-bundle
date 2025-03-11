<?php

declare(strict_types=1);

namespace Nytodev\InertiaSymfony\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class InertiaSymfonyTwigExtension extends AbstractExtension
{
    public function __construct()
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('inertia_render', [InertiaSymfonyRuntime::class, 'render'], ['is_safe' => ['html']]),
            new TwigFunction('inertia_head', [InertiaSymfonyRuntime::class, 'head'], ['is_safe' => ['html']]),
        ];
    }
}
