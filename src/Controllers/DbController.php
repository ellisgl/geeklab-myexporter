<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Authentication\NotLoggedInException;
use App\Core\BaseController;
use App\Core\Template\TwigRenderer;
use GeekLab\Conf\GLConf;
use PDO;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

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

    /**
     * Main page after login.
     *
     * @Todo: Move logic to service or something.
     *
     * @return Response
     * @throws NotLoggedInException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(): Response
    {
        $this->checkAuthenticated();
        $data = ['databases' => []];

        $excludedTables = $this->getExcludedDatabases();
        $dbs = $this->getDatabases();
        foreach ($dbs as $db) {
            if (!in_array($db, $excludedTables, true)) {
                $data['databases'][] = $db;
            }
        }

        $this->response->setContent($this->renderer->render('Main', $data));

        return $this->response;
    }

    /**
     * @Todo: Move logic to service or something.
     *
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

    /**
     * @Todo: Move logic to service or something.
     *
     * @param array $data
     *
     * @return JsonResponse
     * @throws NotLoggedInException
     */
    public function getTables(array $data): JsonResponse
    {
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
        $stmt = $this->pdo->prepare(
            "
            SELECT
              TABLE_NAME AS `table`,
              (DATA_LENGTH + INDEX_LENGTH) AS `size`
            FROM
              information_schema.TABLES
            WHERE
              TABLE_SCHEMA = :database
        "
        );
        $stmt->bindParam(':database', $data['database']);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return new JsonResponse($res);
    }

    /**
     * Get an array of excluded tables.
     *
     * @return array
     * @todo Move to service.
     */
    private function getExcludedDatabases(): array
    {
        $excludedTables = $this->config->get('servers')[(int) $this->request->request->get(
            'host'
        )]['excluded_databases'];
        return is_array($excludedTables) ? $excludedTables : [];
    }

}
