<?php

namespace _ilmComm\Core\DataBase\Traits;

use _ilmComm\Core\DataBase\DataBase;
use _ilmComm\Core\DataBase\ilmMySqli\ilmMySqli_Result;

trait FetchOperation
{
    /**
     * Get from database operation
     *
     * @param string $tableName
     * @param integer|null|array $numRows
     * @param array|string $columns
     * @return ilmMySqli_Result
     */
    public function get(string $tableName, $numRows = null, $columns = '*'): ilmMySqli_Result
    {
        /** @var DataBase $this */
        
        $this->TableName = $tableName;
        $this->columns($columns);

        $this->Query = 'SELECT ' . implode(' ', $this->QueryOptions) . ' ' .
            $this->buildColumns() . ' FROM ' . $this->TableName;
        $stmt = $this->buildQuery($numRows);

        if ($this->isSubQuery) {
            return $this;
        }

        $stmt->execute();
        $res = new ilmMySqli_Result($stmt->get_result());
        $this->reset();
        return $res;
    }
    
    /**
     * Get single data from database operation
     *
     * @param string $tableName
     * @param array|string $columns
     * @param callable $process
     * @return object|array|false|null
     */
    public function getOne(string $tableName, $columns = '*', callable $process = null)
    {
        $res = $this->get($tableName, 1, $columns);
        $process && $res->processOutput($process);
        return $res->fetch();
    }

    /**
     * Get single index value from database
     *
     * @param string $tableName
     * @param string $column
     * @param mixed $d
     * @return mixed
     */
    public function getValue(string $tableName, string $column, $d = "")
    {
        $res = $this->get($tableName, 1, $column)->fetch();
        return rec_arr_val($res, $column, $d);
    }

    /**
     * Has row in database
     *
     * @param string $tableName
     * @return boolean
     */
    public function has(string $tableName): bool
    {
        return (bool) $this->getOne($tableName, '1');
    }

    /**
     * Retrieve array from mysqli result
     *
     * @param \mysqli_result $rslt
     * @param string|int|null $colname
     * @return array
     */
    public static function retrieveArray(\mysqli_result $rslt, $colname = null): array
    {
        $o = [];
        while ($row = $rslt->fetch_assoc()) {
            $o[] = $row;
        }

        $colname && $o = array_column($o, $colname);
        return $o;
    }
}
