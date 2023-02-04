<?php

namespace _ilmComm\Core\DataBase\Traits;

trait GetterMethods
{
    /**
     * Get insert id
     *
     * @return null|integer
     */
    public function getInsertId()
    {
        return $this->DB->insert_id;
    }

    /**
     * Get last row
     *
     * @return null|array
     */
    public function getLastRow()
    {
        return $this->LastRow;
    }

    /**
     * Get sub query
     *
     * @return null|array
     */
    public function getSubQuery()
    {
        if (!$this->isSubQuery) {
            return null;
        }

        array_shift($this->BindParams);
        $val = array(
            'query' => $this->Query,
            'params' => $this->BindParams,
            'alias' => $this->subQueryOptions['alias']
        );
        $this->reset();
        return $val;
    }

    /**
     * Get last error
     *
     * @return string
     */
    public function getLastError(): string
    {
        return trim($this->DB->error);
    }
}
