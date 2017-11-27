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

/**
 * Description of Select
 *
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Select
{

    /**
     *
     * @var \PDOStatement
     */
    protected $query;
    protected $type = \PDO::FETCH_ASSOC;
    protected $link = null;

    /**
     *
     * @param PDOStatement $query
     */
    public function __construct(\PDOStatement $query)
    {
        $this->query = $query;
    }

    /**
     *
     * @return $this
     */
    public function asArray()
    {
        $this->type = \PDO::FETCH_ASSOC;

        return $this;
    }

    /**
     *
     * @param string $class
     * @return $this
     */
    public function asClass(string $class)
    {
        $this->type = \PDO::FETCH_CLASS;
        $this->link = $class;

        return $this;
    }

    /**
     *
     * @param string $function
     * @return $this
     */
    public function asCallback(string $function)
    {
        $this->type = \PDO::FETCH_FUNC;
        $this->link = $function;

        return $this;
    }

    /**
     *
     * @return type
     */
    public function getAll()
    {
        if (empty($this->link)) {
            return $this->query->fetchAll($this->type);
        }
        return $this->query->fetchAll($this->type, $this->link);
    }

    /**
     *
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
