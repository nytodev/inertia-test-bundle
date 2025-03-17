<?php
declare(strict_types=1);

namespace Nytodev\InertiaSymfony\Ssr;

interface GatewayInterface
{
    /**
     * @param array<string, mixed> $page
     */
    public function dispatch(array $page): ?Response;
}
