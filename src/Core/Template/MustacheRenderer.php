<?php declare(strict_types = 1);

namespace App\Core\Template;

use App\Core\Renderer;
use Mustache_Engine;

class MustacheRenderer implements Renderer
{
    private Mustache_Engine $engine;

    public function __construct(Mustache_Engine $engine)
    {
        $this->engine = $engine;
    }

    public function render($template, $data = []) : string
    {
        return $this->engine->render($template, $data);
    }
}
