<?php

namespace Dewep\PDO;

use PDOExecption;
use PDOStatement;

/**
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class PDO
{

    /** @var \PDO */
    protected $db;

    /** @var bool  */
    protected $transaction = false;

    /**
     * @param string $connect
     * @param string $user
     * @param string $pwd
     */
    public function __construct(string $connect, string $user, string $pwd)
    {
        $this->db = new \PDO($connect, $user, $pwd);
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $this->transaction = $this->db->inTransaction();
    }

    /**
     * @param string $query
     * @param array $params
     * @return Select
     * @throws \Exception
     */
    public function select(string $query, array $params = []): Select
    {
        $q = $this->query($query, $params);

        return new Select($q);
    }

    /**
     * @param string $query
     * @param array $params
     * @return PDOStatement
     * @throws \Exception
     */
    protected function query(string $query, array $params = []): PDOStatement
    {
        $e = $this->db->prepare($query);
        if (!empty($params)) {
            array_walk($params, [$this, 'array2json']);
        }

        //--
        if ($this->transaction) {
            try {
                $this->db->beginTransaction();

                if (!empty($params)) {
                    $e->execute($params);
                } else {
                    $e->execute();
                }

                $this->db->commit();
            } catch (\PDOExecption $exc) {
                $this->db->rollBack();

                throw new \Exception($exc->getMessage());
            }
        } //--
        else {
            if (!empty($params)) {
                $e->execute($params);
            } else {
                $e->execute();
            }
        }

        return $e;
    }

    /**
     * @param string $query
     * @param array $params
     * @return int
     * @throws \Exception
     */
    public function insert(string $query, array $params = []): int
    {
        $q = $this->query($query, $params);

        return $this->db->lastInsertId();
    }

    /**
     * @param string $query
     * @param array $params
     * @return int
     * @throws \Exception
     */
    public function update(string $query, array $params = []): int
    {
        $q = $this->query($query, $params);

        return $q->rowCount();
    }

    /**
     * @param string $query
     * @param array $params
     * @return int
     * @throws \Exception
     */
    public function delete(string $query, array $params = []): int
    {
        $q = $this->query($query, $params);

        return $q->rowCount();
    }

    /**
     * @param $value
     * @param string $key
     */
    protected function array2json(&$value, string $key)
    {
        if (
        is_array($value)
        ) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }
    }

}
