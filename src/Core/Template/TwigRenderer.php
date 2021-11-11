<?php

declare(strict_types = 1);

namespace App\Core\Template;

use App\Core\Renderer;
use Twig\Environment as Twig_Environment;

class TwigRenderer implements Renderer
{
    private Twig_Environment $renderer;

    public function __construct(Twig_Environment $renderer)
    {
        $this->renderer = $renderer;
    }

    public function render($template, $data = []) : string
    {
        return $this->renderer->render("$template.html.twig", $data);
    }
}
