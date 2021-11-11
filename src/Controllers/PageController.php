<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Page\InvalidPageException;

class PageController extends BaseController
{
    public function show($params): void
    {
        $slug = $params['slug'];

        try {
            $data['content'] = $this->pageReader->readBySlug($slug);
        } catch (InvalidPageException $e) {
            $this->response->setStatusCode(404);
            $this->response->setContent('404 - Page not found');

            return;
        }

        $html = $this->renderer->render('Page', $data);
        $this->response->setContent($html);
    }
}
