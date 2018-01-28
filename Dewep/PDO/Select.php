<?php

namespace Dewep\PDO;

use PDOStatement;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Select
{

    /** @var PDOStatement */
    protected $query;
    /** @var int */
    protected $type = \PDO::FETCH_ASSOC;
    /** @var string */
    protected $link;

    /**
     * @param PDOStatement $query
     */
    public function __construct(\PDOStatement $query)
    {
        $this->query = $query;
    }

    /**
     * @return Select
     */
    public function asArray(): Select
    {
        $this->type = \PDO::FETCH_ASSOC;

        return $this;
    }

    /**
     * @param string $class
     * @return Select
     */
    public function asClass(string $class): Select
    {
        $this->type = \PDO::FETCH_CLASS;
        $this->link = $class;

        return $this;
    }

    /**
     * @param string $function
     * @return Select
     */
    public function asCallback(string $function):Select
    {
        $this->type = \PDO::FETCH_FUNC;
        $this->link = $function;

        return $this;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        if (empty($this->link)) {
            return $this->query->fetchAll($this->type);
        }

        return $this->query->fetchAll($this->type, $this->link);
    }

    /**
     * @param callable $callback
     */
    public function getChunk(callable $callback)
    {
        if (empty($this->link)) {
            $this->query->setFetchMode($this->type);
        } else {
            $this->query->setFetchMode($this->type, $this->link);
        }

        while ($row = $this->query->fetch()) {
            $callback($row);
        }
    }

}
