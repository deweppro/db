<?php

namespace Dewep\PDO;

/**
 * Class ActiveRecord
 * @package Dewep\PDO
 */
abstract class ActiveRecord
{
    /** @var PDO */
    protected $_dbRead;
    /** @var PDO */
    protected $_dbWrite;
    /** @var string */
    protected $_table;
    /** @var string */
    protected $_index;
    /** @var array */
    protected $_private = [];

    /**
     * @return bool
     */
    final public function save(): bool
    {
        if (empty($this->_index)) {
            return false;
        }

        $query       = [];
        $queryParams = [];
        $params      = $this->getParams();

        try {


            if (empty($this->{$this->_index})) {
                // insert

                foreach ($params as $param => $value) {
                    $query[sprintf('`%s`', $this->php2db($param))] = ':'.$param;

                    $queryParams[':'.$param] = $value;
                }

                $this->{$this->_index} = $this->_dbWrite->insert(
                    sprintf(
                        'INSERT INTO `%s` (%s) VALUES (%s);',
                        $this->_table,
                        implode(', ', array_keys($query)),
                        implode(', ', array_values($query))
                    ),
                    $queryParams
                );

                return $this->load();

            } else {
                // update

                foreach ($params as $param => $value) {
                    $query[] = sprintf('`%s`=:%s', $this->php2db($param), $param);

                    $queryParams[':'.$param] = $value;
                }

                return (bool)$this->_dbWrite->update(
                    sprintf(
                        'UPDATE `%s` SET %s WHERE %s;',
                        $this->_table,
                        implode(', ', $query),
                        sprintf(
                            '`%s`=%s',
                            $this->php2db($this->_index),
                            ':'.$this->_index
                        )
                    ),
                    $queryParams
                );
            }


        } catch (\Exception $e) {
            error_log($e->getMessage(), 0);
        }

        return false;
    }

    /**
     * @return array
     */
    final protected function getParams(bool $hidePrivate = false)
    {
        $use     = get_object_vars($this);
        $default = get_class_vars(self::class);

        if ($hidePrivate) {
            foreach ($this->_private as $param) {
                unset($use[$param]);
            }
        }

        foreach ($default as $param => $value) {
            unset($use[$param]);
        }

        return $use;
    }

    /**
     * @param string $key
     * @return string
     */
    final protected function php2db(string $key): string
    {
        $returnValue = preg_split('/(?=[A-Z])/', $key);

        if (empty($returnValue)) {
            return $key;
        }

        return strtolower(implode('_', $returnValue));
    }

    /**
     * @return bool
     * @throws \Exception
     */
    final public function load(): bool
    {
        $query       = [];
        $queryParams = [];
        $params      = $this->getParams();

        foreach ($params as $param => $value) {
            if ($value === null) {
                continue;
            }

            $query[] = sprintf('`%s`=:%s', $this->php2db($param), $param);

            $queryParams[':'.$param] = $value;
        }

        if (empty($query)) {
            return false;
        }

        $result = $this->_dbRead->select(
            sprintf(
                'SELECT * FROM `%s` WHERE %s LIMIT 1;',
                $this->_table,
                implode(' AND ', $query)
            ),
            $queryParams
        )->asArray()->getOne();

        if (empty($result)) {
            return false;
        }

        $this->fillFromArray($result);

        return true;
    }

    /**
     * @param array $params
     */
    final public function fillFromArray(array $params): void
    {
        foreach ($params as $param => $value) {
            $param = $this->db2php($param);
            if (property_exists($this, $param)) {
                $this->{$param} = $value;
            }
        }
    }

    /**
     * @param string $key
     * @return string
     */
    final protected function db2php(string $key): string
    {
        $key = str_replace('_', ' ', $key);

        return str_replace(' ', '', lcfirst(ucwords($key)));
    }

    /**
     * @return array
     */
    final public function toArray(): array
    {
        return $this->getParams(true);
    }

    /**
     * @param PDO $db
     */
    final protected function setReadDB(PDO $db)
    {
        $this->_dbRead = $db;
    }

    /**
     * @param PDO $db
     */
    final protected function setWriteDB(PDO $db)
    {
        $this->_dbWrite = $db;
    }

}
