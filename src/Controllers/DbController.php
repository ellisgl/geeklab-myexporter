<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Template\TwigRenderer;
use GeekLab\Conf\GLConf;
use PDO;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        $excludedTables = $this->getExcludedDatabases();
        $dbs = $this->getDatabases();
        foreach($dbs as $db) {
            if (!in_array($db, $excludedTables, true)) {
                $data['databases'][] = $db;
            }
        }

        $this->response->setContent($this->renderer->render('Main', $data));

        return $this->response;
    }

    /**
     * @return array
     */
    public function getDatabases(): array
    {
        return array_map(
            static function ($row) {
                return $row['Database'];
            },
            $this->pdo->query('SHOW DATABASES')->fetchAll(PDO::FETCH_ASSOC)
        );

    }

    public function getTables(array $data): JsonResponse {
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->checkAuthenticated();
        $excludedDatabases = $this->getExcludedDatabases();
        if (in_array($data['database'], $excludedDatabases, true)) {
            throw new BadRequestException('Bad Request');
        }

        $dbs = $this->getDatabases();
        if (!in_array($data['database'], $dbs, true)) {
            throw new BadRequestException('Bad Request');
        }

        // Select our db.
        $this->pdo->query("USE `{$data['database']}`")->execute();

        // Get the table info.
        $stmt = $this->pdo->prepare("
            SELECT
              TABLE_NAME AS `table`,
              (DATA_LENGTH + INDEX_LENGTH) AS `size`
            FROM
              information_schema.TABLES
            WHERE
              TABLE_SCHEMA = :database
        ");
        $stmt->bindParam(':database', $data['database']);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return new JsonResponse($res);
    }

    /**
     * Get an array of excluded tables.
     * @todo Move to service.
     *
     * @return array
     */
    private function getExcludedDatabases(): array
    {
        $excludedTables = $this->config->get('servers')[(int)$this->request->request->get('host')]['excluded_databases'];
        return is_array($excludedTables) ? $excludedTables : [];
    }

}
