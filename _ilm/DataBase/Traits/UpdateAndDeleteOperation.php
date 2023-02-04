<?php

namespace _ilmComm\Core\DataBase\Traits;

trait UpdateAndDeleteOperation
{
    public function update(string $tableName, $tableData, $numRows = null): bool
    {
        $this->Query = 'UPDATE ' . $tableName;

        $stmt = $this->buildQuery($numRows, $tableData);
        $status = $stmt->execute();
        $this->reset();
        $this->count = $stmt->affected_rows;

        return $status;
    }

    public function delete($tableName, $numRows = null): bool
    {
        if (count($this->Joins)) {
            $this->Query = 'DELETE ' . preg_replace('/.* (.*)/', '$1', $tableName) . ' FROM ' . $tableName;
        } else {
            $this->Query = 'DELETE FROM ' . $tableName;
        }

        $stmt = $this->buildQuery($numRows);
        $stmt->execute();
        $this->count = $stmt->affected_rows;
        $this->reset();

        return $stmt->affected_rows > -1;
    }
}
