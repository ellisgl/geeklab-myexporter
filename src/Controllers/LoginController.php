<?php

declare(strict_types=1);

namespace App\Controllers;


use \Exception;
use \PDO;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends BaseController
{
    public function index(): void
    {
        $data = [];

        if (Request::METHOD_POST === $this->request->getMethod()) {
            try {
                new PDO(
                    'mysql:host=' . $this->config->get('servers')[(int)$this->request->request->get('host')]['HOST'] . ';',
                    $this->request->request->get('username'),
                    $this->request->request->get('password'),
                    [PDO::ATTR_PERSISTENT => false]
                );

                $this->session->set('loggedIn', 1);
                $this->session->set('dbh', $this->config->get('servers')[(int)$this->request->request->get('host')]['HOST']);
                $this->session->set('dbu', $this->request->request->get('username'));
                $this->session->set('dbp', $this->request->request->get('password'));
            } catch(Exception $e) {
                $this->session->getFlashBag()->add('error', $e->getMessage());
            }

            foreach($this->session->getFlashBag()->get('warning',[]) as $message) {
                $data['warnings'][] = $message;
            }

            foreach($this->session->getFlashBag()->get('error',[]) as $message) {
                $data['errors'][] = $message;
            }
        }

        if (1 === $this->session->get('loggedIn')) {
            // Go to the main page.
            (new RedirectResponse('/db'))->send();
            exit();
        }

        $data['servers'] = $this->config->get('servers');
        $html = $this->renderer->render('Login', $data);
        $this->response->setContent($html);
    }
}
