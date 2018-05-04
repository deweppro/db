<?php

namespace Dewep;

/**
 * Class Mysql
 * @package Dewep
 */
class Mysql extends PDO\PDO
{
    /**
     * Mysql constructor.
     * @param string $host
     * @param int $port
     * @param string $dbname
     * @param string $user
     * @param string $pwd
     */
    public function __construct(string $host, int $port, string $dbname, string $user, string $pwd)
    {
        parent::__construct(
            sprintf("mysql:host=%s;port=%s;dbname=%s;charset=utf8", $host, $port, $dbname),
            $user,
            $pwd
        );
    }

}
