<?php

declare(strict_types=1);

namespace Nytodev\InertiaSymfony\Ssr;

final class Response
{
    public function __construct(public string $head, public string $body)
    {
    }
}
