<?php

namespace _ilmComm\Core\DataBase\Traits;

use _ilmComm\Core\DataBase\DataBase;
use _ilmComm\Exceptions\DatabaseException;

trait ConditionSetters
{
    /**
     * Set fetch columns
     *
     * @param array|string $tableCols
     * @return DataBase
     */
    public function columns($tableCols): DataBase
    {
        // create array if cols is not array
        if (!is_array($tableCols)) {
            $tableCols = explode(",", $tableCols);
        }

        // assign
        $this->Columns = array_merge($this->Columns, $tableCols);
        return $this;
    }

    /**
     * Where condition
     *
     * @param string $whereProp condition propery name (Eg: column title)
     * @param mixed $whereValue
     * @param string $operator
     * @param string $cond AND|OR
     * @return DataBase
     */
    public function where(string $whereProp, $whereValue = 'DBNULL', string $operator = '=', string $cond = 'AND'): DataBase
    {
        // empty condition prefix if no previous condition
        if (count($this->Where) == 0) {
            $cond = '';
        }

        // assign
        $this->Where[] = [$cond, $whereProp, $operator, $whereValue];
        return $this;
    }

    /**
     * Having condition
     *
     * @param string $havingProp
     * @param mixed $havingValue
     * @param string $operator
     * @param string $cond
     * @return DataBase
     */
    public function having(string $havingProp, $havingValue = 'DBNULL', string $operator = '=', string $cond = 'AND'): DataBase
    {
        // if cond value is array, get first element as operator and value
        if (is_array($havingValue) && ($key = key($havingValue)) != '0') {
            $operator = $key;
            $havingValue = $havingValue[$key];
        }

        // empty condition prefix if no previous condition
        if (count($this->Having) == 0) {
            $cond = '';
        }

        // assign
        $this->Having[] = [$cond, $havingProp, $operator, $havingValue];
        return $this;
    }

    /**
     * Table joins
     *
     * @param string $joinTable
     * @param string $joinCondition
     * @param string $joinType
     * @param string $joinColums
     * @return DataBase
     */
    public function join(string $joinTable, string $joinCondition, string $joinType = '', $joinColums = null): DataBase
    {
        // allowed types array
        $allowedTypes = ['LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 'RIGHT OUTER', 'NATURAL'];

        // trim and upper join type
        $joinType = strtoupper(trim($joinType));

        if (!empty($joinType) && !in_array($joinType, $allowedTypes)) {
            // join type not in list
            throw DatabaseException::create(1, $joinType);
        }

        if ($joinColums === null) {
            // join columns set default
            $joinColums = $joinTable . ".*";
        }

        // assign
        $this->columns($joinColums);
        $this->Joins[] = [$joinType, $joinTable, $joinCondition];
        return $this;
    }

    /**
     * Order by
     *
     * @param string $orderByField
     * @param string $orderbyDirection
     * @param array|string $customFieldsOrRegExp
     * @return DataBase
     */
    public function orderBy(string $orderByField, string $orderbyDirection = 'DESC', $customFieldsOrRegExp = null): DataBase
    {
        // allowed order by direction
        $allowedDirection = ['ASC', 'DESC'];

        // trim and upper order by direction
        $orderbyDirection = strtoupper(trim($orderbyDirection));

        // sanitize order by field: remove worthless characters
        $orderByField = preg_replace("/[^ -a-z0-9\.\(\),_`\*\'\"]+/i", '', $orderByField);

        if (empty($orderbyDirection) || !in_array($orderbyDirection, $allowedDirection)) {
            // order by direction empty or not in list
            throw DatabaseException::create(2, $orderbyDirection);
        }

        if (is_array($customFieldsOrRegExp)) {
            // regex direction is array
            // use of mysql FIELD(value, val1, val2..) as sorting
            foreach ($customFieldsOrRegExp as $key => $value) {
                // sanitize value: remove worthless characters
                $customFieldsOrRegExp[$key] = preg_replace("/[^\x80-\xff-a-z0-9\.\(\),_` ]+/i", '', $value);
            }

            $orderByField = 'FIELD (' . $orderByField . ', "' . implode('","', $customFieldsOrRegExp) . '")';
        } elseif (is_string($customFieldsOrRegExp)) {
            // regex direction is string
            // use of mysql REGEXP command as sorting
            $orderByField = $orderByField . " REGEXP '" . $customFieldsOrRegExp . "'";
        } elseif ($customFieldsOrRegExp !== null) {
            // invalid regex direction
            throw DatabaseException::create(3, $customFieldsOrRegExp);
        }

        // assign values
        $this->OrderBy[$orderByField] = $orderbyDirection;
        return $this;
    }

    /**
     * Group by
     *
     * @param string $groupByField
     * @return DataBase
     */
    public function groupBy(string $groupByField): DataBase
    {
        // sanitize group by field: remove worthless characters
        $groupByField = preg_replace("/[^-a-z0-9\.\(\),_\* <>=!]+/i", '', $groupByField);

        // assign value
        $this->GroupBy[] = $groupByField;
        return $this;
    }

    /**
     * On duplicate
     *
     * @param array $updateColumns
     * @param integer $lastInsertId
     * @return DataBase
     */
    public function onDuplicate(array $updateColumns, int $lastInsertId = null): DataBase
    {
        $this->LastInsertId = $lastInsertId;
        $this->UpdateColumns = $updateColumns;
        return $this;
    }
}
