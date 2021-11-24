<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Template\TwigRenderer;
use GeekLab\Conf\GLConf;
use PDO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class DbController extends BaseController
{
    private PDO $pdo;

    public function __construct(
        GLConf $config,
        TwigRenderer $renderer,
        Request $request,
        Response $response,
        Session $session,
        PDO $pdo
    ) {
        parent::__construct($config, $renderer, $request, $response, $session);
        $this->pdo = $pdo;
    }

    public function index(): Response
    {
        $this->checkAuthenticated();
        $data = ['databases' => []];

        $excludedTables = $this->config->get('servers')[(int)$this->request->request->get('host')]['excluded_tables'];
        $servers = $this->config->get('servers');

        $res = $this->pdo->query('SHOW DATABASES')->fetchAll(PDO::FETCH_ASSOC);
        foreach($res as $row) {
            if (!in_array($row['Database'], $excludedTables, true)) {
                $data['databases'][] = $row['Database'];
            }
        }

        $this->response->setContent($this->renderer->render('Main', $data));

        return $this->response;
    }

}
