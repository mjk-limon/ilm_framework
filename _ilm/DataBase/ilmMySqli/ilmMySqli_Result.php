<?php

namespace _ilmComm\Core\DataBase\ilmMySqli;

class ilmMySqli_Result
{
    /**
     * Mysqli result
     *
     * @var \mysqli_result
     */
    private $MysqliResult;

    /**
     * Process output
     *
     * @var callable
     */
    private $ProcessOutput;

    /**
     * Object constructor
     *
     * @param \mysqli_result|array $res
     */
    public function __construct($res)
    {
        if (false === $res || !isset($res)) {
            return;
        }

        if (!is_array($res)) {
            $this->MysqliResult = $res;
            $this->num_rows = $this->MysqliResult->num_rows;
            return;
        }

        $this->MysqliMultiResult = $res;
    }

    /**
     * Call unattended function
     *
     * @param string $Mthd
     * @param mixed $Arg
     * @return mixed
     */
    public function __call(string $Mthd, $Arg)
    {
        return $this->MysqliResult->{$Mthd}(...$Arg);
    }

    /**
     * Checks result exists
     *
     * @return boolean
     */
    public function checkResult(): bool
    {
        return (bool) $this->MysqliResult;
    }

    /**
     * Process output
     *
     * @param callable $c
     * @return ilmMySqli_Result
     */
    public function processOutput(callable $c): ilmMySqli_Result
    {
        $this->ProcessOutput = $c;
        return $this;
    }

    /**
     * Fetch
     *
     * @return null|array|object
     */
    public function fetch()
    {
        if ($o = $this->MysqliResult->fetch_assoc()) {
            if (is_callable($this->ProcessOutput)) {
                return call_user_func($this->ProcessOutput, $o);
            }

            return $o;
        }

        return null;
    }

    /**
     * Fetch multiple data
     *
     * @return void
     */
    public function fetch_all()
    {
        if (property_exists($this, 'MysqliMultiResult')) {
            $this->MysqliResult = current($this->MysqliMultiResult)->MysqliResult;
            
            if ($this->MysqliResult instanceof \mysqli_result) {
                $cRow = $this->fetch();

                if ($cRow == null) {
                    array_shift($this->MysqliMultiResult);
                    return $this->fetch_all();
                }

                return $cRow;
            }
        }

        return null;
    }

    /**
     * Convert result to array
     *
     * @param string|int|null $colname
     * @return array
     */
    public function toArray($colname = null): array
    {
        $o = [];
        while ($row = $this->MysqliResult->fetch_assoc()) {
            $o[] = $row;
        }

        $colname && $o = array_column($o, $colname);
        return $o;
    }
}
