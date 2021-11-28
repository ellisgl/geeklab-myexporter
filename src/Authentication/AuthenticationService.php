<?php

declare(strict_types=1);

namespace App\Authentication;

use Symfony\Component\HttpFoundation\Session\Session;

class AuthenticationService
{
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @throws NotLoggedInException
     */
    public function checkAuthenticated(): void
    {
        if (!$this->session->get('loggedIn')) {
            throw new NotLoggedInException('NOT LOGGED IN');
        }
    }
}
