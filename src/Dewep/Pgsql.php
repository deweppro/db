<?php

namespace Dewep;

/**
 * Class Pgsql
 *
 * @package Dewep
 */
class Pgsql extends PDO\PDO
{
    /**
     * Pgsql constructor.
     *
     * @param string $host
     * @param int    $port
     * @param string $dbname
     * @param string $user
     * @param string $pwd
     */
    public function __construct(string $host, int $port, string $dbname, string $user, string $pwd)
    {
        parent::__construct(
            sprintf("pgsql:host=%s;port=%s;dbname=%s;charset=utf8", $host, $port, $dbname),
            $user,
            $pwd
        );
    }

}
