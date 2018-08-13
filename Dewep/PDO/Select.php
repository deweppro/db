<?php

namespace Dewep\PDO;

use PDOStatement;

/**
 * Class Select
 * @package Dewep\PDO
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
     * Select constructor.
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
    public function asCallback(string $function): Select
    {
        $this->type = \PDO::FETCH_FUNC;
        $this->link = $function;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAll()
    {
        $this->fetchMode();
        $data = $this->query->fetchAll();
        $this->query->closeCursor();

        return $data;
    }

    /**
     * @return Select
     */
    private function fetchMode(): Select
    {
        if (empty($this->link)) {
            $this->query->setFetchMode($this->type);
        } else {
            $this->query->setFetchMode($this->type, $this->link);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOne()
    {
        $this->fetchMode();
        $data = $this->query->fetch();
        $this->query->closeCursor();

        return $data;
    }

    /**
     * @param callable $callback
     */
    public function getChunk(callable $callback)
    {
        $this->fetchMode();

        while ($row = $this->query->fetch()) {
            $callback($row);
        }

        $this->query->closeCursor();
    }

}
