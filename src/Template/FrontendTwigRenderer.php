<?php

declare(strict_types=1);

namespace App\Template;

use App\Core\Renderer;
use App\Menu\MenuReader;

class FrontendTwigRenderer implements FrontendRenderer
{
    private Renderer $renderer;
    private MenuReader $menuReader;

    public function __construct(Renderer $renderer, MenuReader $menuReader)
    {
        $this->renderer = $renderer;
        $this->menuReader = $menuReader;
    }

    public function render($template, $data = []): string
    {
        $data = array_merge($data, ['menuItems' => $this->menuReader->readMenu(),]);
        return $this->renderer->render($template, $data);
    }
}
