<?php

/*
 * The MIT License
 *
 * Copyright 2017 Mikhail Knyazhev <markus621@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Dewep\PDO;

use \PDOStatement;
use \PDOExecption;
use Dewep\PDO\Select;

/**
 *
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class PDO
{

    /**
     *
     * @var \PDO
     */
    protected $db;

    /**
     *
     * @var bool
     */
    protected $transaction = false;

    public function __construct(string $connect, string $user, string $pwd)
    {
        $this->db = new \PDO($connect, $user, $pwd);
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $this->transaction = $this->db->inTransaction();
    }

    /**
     *
     * @param array $data
     * @return string
     */
    protected function array2json(array &$value, string $key): string
    {
        $value = json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    /**
     *
     * @param string $query
     * @param array $params
     * @return PDOStatement
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

                if (!empty($params))
                    $e->execute($params);
                else
                    $e->execute();

                $this->db->commit();
            } catch (\PDOExecption $exc) {
                $this->db->rollBack();

                throw new \Exception($exc->getMessage());
            }
        }
        //--
        else {
            if (!empty($params))
                $e->execute($params);
            else
                $e->execute();
        }

        return $e;
    }

    /**
     *
     * @param string $query
     * @param array $params
     * @return Select
     */
    public function select(string $query, array $params = []): Select
    {
        $q = $this->query($query, $params);
        return new Select($q);
    }

    /**
     *
     * @param string $query
     * @param array $params
     * @return int
     */
    public function insert(string $query, array $params = []): int
    {
        $q = $this->query($query, $params);
        return $this->db->lastInsertId();
    }

    /**
     *
     * @param string $query
     * @param array $params
     * @return int
     */
    public function update(string $query, array $params = []): int
    {
        $q = $this->query($query, $params);
        return $q->rowCount();
    }

    /**
     *
     * @param string $query
     * @param array $params
     * @return int
     */
    public function delete(string $query, array $params = []): int
    {
        $q = $this->query($query, $params);
        return $q->rowCount();
    }

}
