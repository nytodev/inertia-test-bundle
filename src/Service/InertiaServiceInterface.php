<?php

declare(strict_types=1);

namespace Nytodev\InertiaSymfony\Service;

use Symfony\Component\HttpFoundation\Response;

interface InertiaServiceInterface
{
    /**
     * @param array<string, mixed> $props
     */
    public function render(string $component, array $props = []): Response;

    public function getVersion(): ?string;

    public function getLocation(string $url): Response;
}
