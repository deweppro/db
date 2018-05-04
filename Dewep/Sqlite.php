<?php

namespace Dewep;

/**
 * Class Sqlite
 * @package Dewep
 */
class Sqlite extends PDO\PDO
{
    /**
     * Sqlite constructor.
     * @param string $file
     */
    public function __construct(string $file)
    {
        parent::__construct(sprintf('sqlite:%s', $file));
    }

}
