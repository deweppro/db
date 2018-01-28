<?php

namespace Dewep;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Mysql extends PDO\PDO
{

    public function __construct(
        string $host,
        int $port,
        string $dbname,
        string $user,
        string $pwd
    ) {
        parent::__construct(
            sprintf("mysql:host=%s;port=%s;dbname=%s;charset=utf8", $host, $port, $dbname),
            $user,
            $pwd
        );
    }

}
