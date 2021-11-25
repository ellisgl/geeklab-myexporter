<?php

declare(strict_types=1);

namespace App\Core;

interface Renderer
{
    /**
     * Return a rendered template.
     *
     * @param       $template
     * @param array $data
     *
     * @return string
     */
    public function render($template, array $data = []): string;
}
