<?php

namespace Nytodev\InertiaSymfony\Service;

enum InertiaHeader: string
{
    case XInertia = 'X-Inertia';
    case XInertiaVersion = 'X-Inertia-Version';
    case XInertiaLocation = 'X-Inertia-Location';
}
