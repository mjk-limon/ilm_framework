<?php

namespace _ilmComm\Core\DataBase\Traits;

use _ilmComm\Exceptions\DatabaseException;

trait IUD_OperationHelper
{
    /**
     * Get table structure
     *
     * @param string $tableName
     * @return array
     */
    public function getTableStructure(string $tableName): array
    {
        $allTables = config('db-structure');
        return rec_arr_val($allTables, $tableName, []);
    }

    /**
     * Build table data
     *
     * @param string $t
     * @param string $tName
     * @param array $postVals
     * @return array
     */
    public function buildTableData(string $t, string $tName, array $postVals): array
    {
        $tableData = array();
        $td_arr = $this->getTableStructure($tName);

        foreach ($td_arr as $tCol => $tiArr) {
            $postKey = rec_arr_val($tiArr, 'Pseudo', $tCol);
            $tableData[$tCol] = rec_arr_val($postVals, $postKey, null);

            if (($t == "INSERT") && ($tableData[$tCol] === null)) {
                if (rec_arr_val($tiArr, 'Extra') == 'auto_increment') {
                    continue;
                }

                if (array_key_exists('Default', $tiArr)) {
                    $tableData[$tCol] = $tiArr['Default'];
                    continue;
                }

                throw new DatabaseException("{$tCol} missing", 0);
            }
        }

        return array_filter($tableData, function ($val) {
            return $val !== null;
        });
    }
}