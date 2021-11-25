<?php

declare(strict_types = 1);

namespace App\Core\Template;

use App\Core\Renderer;
use Twig\Environment as Twig_Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TwigRenderer implements Renderer
{
    private Twig_Environment $renderer;

    public function __construct(Twig_Environment $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render($template, array $data = []) : string
    {
        return $this->renderer->render("$template.html.twig", $data);
    }
}
