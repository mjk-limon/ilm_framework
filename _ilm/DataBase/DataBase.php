<?php

namespace _ilmComm\Core\DataBase;

use _ilmComm\Core\DataBase\ilmMySqli\ilmMySqli_Result;
use _ilmComm\Core\DataBase\Traits\ConditionSetters;
use _ilmComm\Core\DataBase\Traits\GetterMethods;
use _ilmComm\Core\DataBase\Traits\MysqlFunctions;
use _ilmComm\Exceptions\DatabaseException;

class DataBase
{
    use ConditionSetters,
        MysqlFunctions,
        GetterMethods;

    /**
     * Mysqli object
     *
     * @var \mysqli
     */
    protected $DB;

    /**
     * SQL Query string
     *
     * @var string
     */
    public $Query = "";

    /**
     * Query options
     *
     * @var array
     */
    protected $QueryOptions = [];

    /**
     * Table name
     *
     * @var string
     */
    protected $TableName = '';

    /**
     * Fetch columns
     *
     * @var array
     */
    protected $Columns = [];

    /**
     * Table joins
     *
     * @var array
     */
    protected $Joins = [];

    /**
     * SQL where conditions
     *
     * @var array
     */
    protected $Where = [];

    /**
     * SQL having conditions
     *
     * @var array
     */
    protected $Having = [];

    /**
     * SQL order by columns
     *
     * @var array
     */
    protected $OrderBy = [];

    /**
     * SQL group by columns
     *
     * @var array
     */
    protected $GroupBy = [];

    /**
     * SQL fetch limit
     *
     * @var integer
     */
    protected $Limit = 0;

    /**
     * Bind params for parameterized statement
     *
     * @var array
     */
    protected $BindParams = [''];

    /**
     * Query type is sub query
     *
     * @var boolean
     */
    protected $isSubQuery = false;

    /**
     * Sub query options
     *
     * @var array
     */
    protected $subQueryOptions = [];

    /**
     * Result row count (num rows)
     *
     * @var integer
     */
    protected $Count = 0;

    /**
     * Last inserted row
     *
     * @var array
     */
    protected $LastRow = [];

    /**
     * Last inserted id
     *
     * @var null|integer
     */
    protected $LastInsertId = null;

    /**
     * Update columns
     *
     * @var array
     */
    protected $UpdateColumns = null;

    /**
     * Database instance
     *
     * @var DataBase
     */
    protected static $instance;

    /**
     * Database consructor
     */
    public function __construct()
    {
        global $conn;
        $this->DB = $conn;
    }

    /**
     * Get current instance
     *
     * @return DataBase
     */
    public static function getInstance(): DataBase
    {
        $className = static::class;
        self::$instance = new $className();
        return self::$instance;
    }

    /**
     * Escape string
     *
     * @param string $str
     * @return string
     */
    public function escaped(string $str): string
    {
        return $this->DB->real_escape_string($str);
    }

    /**
     * Raw query
     * 
     * @param string $query
     * @param array $bindParams
     * @return ilmMySqli_Result
     */
    public function rawQuery(string $query, array $bindParams = [])
    {
        // init and prepare query
        $params = [''];
        $this->Query = $query;
        $stmt = $this->prepareQuery();

        // bind parameters to prepared query
        if (is_array($bindParams)) {
            foreach ($bindParams as $prop => $val) {
                $params[0] .= $this->determineType($val);
                array_push($params, $bindParams[$prop]);
            }

            call_user_func_array([$stmt, 'bind_param'], $this->refValues($params));
        }

        // execute and fetch result
        $stmt->execute();
        $this->Count = $stmt->affected_rows;
        $res = new ilmMySqli_Result($stmt->get_result());
        $this->reset();
        return $res;
    }

    /**
     * Query operation
     *
     * @param string $query
     * @param integer $numRows
     * @return ilmMySqli_Result
     */
    public function query(string $query, int $numRows = null)
    {
        $this->Query = $query;
        $stmt = $this->buildQuery($numRows);
        $stmt->execute();
        $res = new ilmMySqli_Result($stmt->get_result());
        $this->reset();
        return $res;
    }

    /**
     * Sub query operation
     *
     * @param string $subQueryAlias
     * @return DataBase
     */
    public static function subQuery(string $subQueryAlias = ""): DataBase
    {
        $db = new self();
        $db->isSubQuery = true;
        $db->subQueryOptions["alias"] = $subQueryAlias;
        return $db;
    }

    /**
     * Prepare query
     *
     * @throws DatabaseException
     * @return \mysqli_stmt
     */
    protected function prepareQuery(): \mysqli_stmt
    {
        $stmt = $this->DB->prepare($this->Query);

        if ($stmt !== false) {
            return $stmt;
        }

        throw DatabaseException::create(5, $this->DB->error, $this->Query);
    }

    /**
     * Bind prepared query parameter single
     *
     * @param mixed $value
     * @return void
     */
    protected function bindParam($value): void
    {
        $this->BindParams[0] .= $this->determineType($value);
        array_push($this->BindParams, $value);
    }

    /**
     * Bind prepared query parameters from array
     *
     * @param array $values
     * @return void
     */
    protected function bindParams(array $values): void
    {
        foreach ($values as $value) {
            $this->bindParam($value);
        }
    }

    /**
     * Reference values for param bind
     *
     * @param array $arr
     * @return array
     */
    protected function refValues(array &$arr): array
    {
        $refs = [];
        foreach ($arr as $key => $value) {
            $refs[$key] = &$arr[$key];
        }
        return $refs;
    }

    /**
     * Build pair
     *
     * @param string $operator
     * @param mixed $value
     * @return string
     */
    protected function buildPair(string $operator, $value): string
    {
        if (!is_object($value)) {
            $this->bindParam($value);
            return ' ' . $operator . ' ? ';
        }

        $subQuery = $value->getSubQuery();
        $this->bindParams($subQuery['params']);
        return " " . $operator . " (" . $subQuery['query'] . ") " . $subQuery['alias'];
    }

    /**
     * Build query
     *
     * @param integer|array|null $nr
     * @param array $td
     * @return \mysqli_stmt
     */
    protected function buildQuery($nr = null, array $td = null): \mysqli_stmt
    {
        // Build query
        foreach ($this->buildQueryMethods($nr, $td) as $QueryCond) {
            list($Mthd, $Param) = array_replace(['', []], $QueryCond);

            if (method_exists($this, $Mthd)) {
                $this->{$Mthd}(...$Param);
            }
        }

        // Prepare query
        $stmt = $this->prepareQuery();

        // Bind parameters to statement if any
        if (count($this->BindParams) > 1) {
            call_user_func_array([$stmt, 'bind_param'], $this->refValues($this->BindParams));
        }

        return $stmt;
    }

    /**
     * Build query methods
     *
     * @param null|integer $numRows
     * @param null|array $tableData
     * @return array
     */
    private function buildQueryMethods($numRows, $tableData): array
    {
        return array(
            array('buildJoin'),
            array('buildInsertQuery', [$tableData]),
            array('buildCondition', ['WHERE', $this->Where]),
            array('buildGroupBy'),
            array('buildCondition', ['HAVING', $this->Having]),
            array('buildOrderBy'),
            array('buildLimit', [$numRows]),
            array('buildOnDuplicate', [$tableData])
        );
    }

    /**
     * Build data pairs (Parameterized Query for insert/update/delete) in query statements
     *
     * @param array $tableData
     * @param array $tableColumns
     * @param boolean $isInsert
     * @return void
     */
    public function buildDataPairs(array $tableData, array $tableColumns, bool $isInsert): void
    {
        foreach ($tableColumns as $column) {
            $value = $tableData[$column];

            if (!$isInsert) {
                if (strpos($column, '.') === false) {
                    $this->Query .= '`' . $column . '` = ';
                } else {
                    $this->Query .= str_replace('.', '.`', $column) . '` = ';
                }
            }

            // Simple value
            if (!is_array($value)) {
                $this->bindParam($value);
                $this->Query .= '?, ';
                continue;
            }

            // Function value
            $key = key($value);
            $val = $value[$key];
            switch ($key) {
                case '[I]':
                    $this->Query .= $column . $val . ', ';
                    break;
                case '[F]':
                    $this->Query .= $val[0] . ', ';
                    isset($val[1]) && $val[1] && $this->bindParams($val[1]);
                    break;
                case '[N]':
                    $qval = ($val == null) ? $column : $val;
                    $this->Query .= '!' . $qval . ', ';
                    break;
                default:
                    throw DatabaseException::create(8);
            }
        }
        $this->Query = rtrim($this->Query, ', ');
    }

    /**
     * Build fetch columns in query statement
     *
     * @return string
     */
    protected function buildColumns(): string
    {
        if (!$this->Columns) {
            return "*";
        }

        return implode(",", array_filter(array_unique($this->Columns)));
    }

    /**
     * Build group by in query statement
     *
     * @return void
     */
    protected function buildGroupBy(): void
    {
        if (empty($this->GroupBy)) {
            return;
        }

        $this->Query .= ' GROUP BY ';

        foreach ($this->GroupBy as $key => $value) {
            $this->Query .= $value . ', ';
        }

        $this->Query = rtrim($this->Query, ', ') . ' ';
    }

    /**
     * Build order by in query statement
     *
     * @return void
     */
    protected function buildOrderBy(): void
    {
        if (empty($this->OrderBy)) {
            return;
        }

        $this->Query .= ' ORDER BY ';
        foreach ($this->OrderBy as $prop => $value) {
            if (strtolower(str_replace(' ', '', $prop)) == 'rand()') {
                $this->Query .= 'rand(), ';
            } else {
                $this->Query .= $prop . ' ' . $value . ', ';
            }
        }

        $this->Query = rtrim($this->Query, ', ') . ' ';
    }

    /**
     * Build limit in query statement
     *
     * @param int|null|array $numRows
     * @return void
     */
    protected function buildLimit($numRows): void
    {
        if (!isset($numRows)) {
            return;
        }

        if (is_array($numRows)) {
            $this->Query .= ' LIMIT ' . (int) $numRows[0] . ', ' . (int) $numRows[1];
            return;
        }

        $this->Query .= ' LIMIT ' . (int) $numRows;
    }

    /**
     * Build join in query statement
     *
     * @return void
     */
    protected function buildJoin(): void
    {
        if (empty($this->Joins)) {
            return;
        }

        foreach ($this->Joins as $data) {
            list($joinType, $joinStr, $joinCondition) = $data;

            $this->Query .= ' ' . $joinType . ' JOIN ' . $joinStr .
                (false !== stripos($joinCondition, 'using') ? ' ' : ' on ')
                . $joinCondition;
        }
    }

    /**
     * Build conditions
     *
     * @param string $operator
     * @param array $conditions
     * @return void
     */
    protected function buildCondition(string $operator, array &$conditions): void
    {
        if (empty($conditions)) {
            return;
        }

        //Prepare the where portion of the query
        $this->Query .= ' ' . $operator;

        foreach ($conditions as $cond) {
            list($concat, $varName, $operator, $val) = $cond;
            $this->Query .= ' ' . $concat . ' ' . $varName;

            switch (strtolower($operator)) {
                case 'not in':
                case 'in':
                    $comparison = ' ' . $operator . ' (';

                    foreach ($val as $v) {
                        $comparison .= ' ?,';
                        $this->bindParam($v);
                    }

                    $this->Query .= rtrim($comparison, ',') . ' ) ';
                    break;
                case 'not between':
                case 'between':
                    $this->Query .= " $operator ? AND ? ";
                    $this->bindParams($val);
                    break;
                case 'not exists':
                case 'exists':
                    $this->Query .= $operator . $this->buildPair('', $val);
                    break;
                default:
                    if (is_array($val)) {
                        $this->bindParams($val);
                    } elseif ($val === null) {
                        $this->Query .= ' ' . $operator . ' NULL';
                    } elseif ($val != 'DBNULL' || $val == '0') {
                        $this->Query .= $this->buildPair($operator, $val);
                    }
            }
        }
    }

    /**
     * Determine data type
     *
     * @param mixed $item
     * @return string
     */
    protected function determineType($item): string
    {
        switch (gettype($item)) {
            case 'NULL':
            case 'string':
                return 's';
                break;

            case 'boolean':
            case 'integer':
                return 'i';
                break;

            case 'blob':
                return 'b';
                break;

            case 'double':
                return 'd';
                break;
        }

        return '';
    }

    /**
     * Reset all
     *
     * @return void
     */
    protected function reset(): void
    {
        $this->Where = [];
        $this->Joins = [];
        $this->OrderBy = [];
        $this->GroupBy = [];
        $this->BindParams = [''];
        $this->Query = null;
        $this->QueryOptions = [];
        $this->TableName = '';
        $this->Columns = [];
        $this->LastInsertId = null;
        $this->UpdateColumns = null;
    }
}
