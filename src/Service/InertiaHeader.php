<?php

namespace App\Service;

enum InertiaHeader: string
{
    case XInertia = 'X-Inertia';
    case XInertiaVersion = 'X-Inertia-Version';
    case XInertiaLocation = 'X-Inertia-Location';
}
