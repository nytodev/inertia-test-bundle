<?php
declare(strict_types=1);

namespace Nytodev\InertiaSymfony\Service;

use Symfony\Component\HttpFoundation\Response;

interface InertiaServiceInterface
{

//    public function share(string $key, mixed $default = null): void;

    public function render(string $component, array $props = []): Response;
}
