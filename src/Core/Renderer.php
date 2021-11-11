<?php

declare(strict_types=1);

namespace App\Core;

interface Renderer
{
    public function render($template, $data = []): string;
}
