<?php

namespace _ilmComm\Core\DataBase\Traits;

use _ilmComm\Exceptions\DatabaseException;

trait InsertAndReplaceOperation
{
    /**
     * Insert into database
     *
     * @param string $tableName
     * @param array $insertData
     * @param array $duplicateCheck
     * @return int|boolean last insert id or true/false
     */
    public function insert(string $tableName, array $insertData, array $duplicateCheck = [])
    {
        if (count($duplicateCheck) > 0) {
            foreach ($duplicateCheck as $k) {
                $this->where($k, $insertData[$k]);
            }

            if ($exists = $this->getOne($tableName)) {
                $this->LastRow = $exists;
                throw DatabaseException::create(7);
            }
        }

        return $this->buildInsert($tableName, $insertData, 'INSERT');
    }

    /**
     * Multiple data insert at once
     *
     * @param string $tableName
     * @param array $multiInsertData
     * @param array|null $dataKeys
     * @return array|false sets of insert id or false
     */
    public function insertMulti(string $tableName, array $multiInsertData, array $dataKeys = null): array
    {
        $ids = [];
        foreach ($multiInsertData as $insertData) {
            if ($dataKeys !== null) {
                // apply column-names if given, else assume they're already given in the data
                $insertData = array_combine($dataKeys, $insertData);
            }

            $id = $this->insert($tableName, $insertData);
            if (!$id) {
                return false;
            }
            $ids[] = $id;
        }

        return $ids;
    }

    /**
     * Replace data
     *
     * @param string $tableName
     * @param array $insertData
     * @return boolean
     */
    public function replace(string $tableName, array $insertData): bool
    {
        return $this->buildInsert($tableName, $insertData, 'REPLACE');
    }

    /**
     * Build insertion
     *
     * @param string $tableName
     * @param array $insertData
     * @param string $operation INSERT/REPLACE
     * @return integer|boolean last insert id or true/false
     */
    private function buildInsert(string $tableName, array $insertData, string $operation)
    {
        $this->Query = $operation . ' ' . implode(' ', $this->QueryOptions) . ' INTO ' . $tableName;
        $stmt = $this->buildQuery(null, $insertData);
        $status = $stmt->execute();
        $haveOnDuplicate = !empty($this->UpdateColumns);
        $this->reset();
        $this->Count = $stmt->affected_rows;

        if ($stmt->affected_rows < 1) {
            // in case of onDuplicate() usage, if no rows were inserted
            if ($status && $haveOnDuplicate) {
                return true;
            }

            throw new DatabaseException($stmt->error, $stmt->errno);
        }

        if ($stmt->insert_id > 0) {
            return $stmt->insert_id;
        }

        return true;
    }

    /**
     * Build on duplicate insertion
     *
     * @param null|array $tableData
     * @return void
     */
    protected function buildOnDuplicate($tableData): void
    {
        if (is_array($this->UpdateColumns) && !empty($this->UpdateColumns)) {
            $this->Query .= ' ON DUPLICATE KEY UPDATE ';
            if ($this->LastInsertId) {
                $this->Query .= $this->LastInsertId . '=LAST_INSERT_ID (' . $this->LastInsertId . '), ';
            }

            foreach ($this->UpdateColumns as $key => $val) {
                // skip all params without a value
                if (is_numeric($key)) {
                    $this->UpdateColumns[$val] = '';
                    unset($this->UpdateColumns[$key]);
                } else {
                    $tableData[$key] = $val;
                }
            }
            $this->buildDataPairs($tableData, array_keys($this->UpdateColumns), false);
        }
    }

    /**
     * Build insert statement
     *
     * @param null|array $tableData
     * @return void
     */
    protected function buildInsertQuery($tableData): void
    {
        if (!is_array($tableData)) {
            return;
        }

        $isInsert = preg_match('/^[INSERT|REPLACE]/', $this->Query);
        $dataColumns = array_keys($tableData);
        if ($isInsert) {
            if (isset($dataColumns[0])) {
                $this->Query .= ' (`' . implode('`, `', $dataColumns) . '`) ';
            }
            $this->Query .= ' VALUES (';
        } else {
            $this->Query .= ' SET ';
        }

        $this->buildDataPairs($tableData, $dataColumns, $isInsert);

        if ($isInsert) {
            $this->Query .= ')';
        }
    }
}
