<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;

class HomepageController extends BaseController
{
    public function index(): void
    {
        $data = [
            // 'name' => $this->request->getParameter('name', 'stranger'),
            'name' => $this->request->query->get('name', 'strange'),
        ];
        $html = $this->renderer->render('Homepage', $data);
        $this->response->setContent($html);
    }

    public function test(): void
    {
        $this->response->setContent('XYZ');
    }
}
