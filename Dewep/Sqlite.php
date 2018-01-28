<?php

namespace Dewep;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Sqlite extends PDO\PDO
{

    public function __construct(string $file)
    {
        parent::__construct(sprintf('sqlite:%s', $file), null, null);
    }

}
