<?php

namespace _ilmComm\Core\DataBase\Traits;

trait TableIndexOperation
{
    /**
     * Add table indexes operation
     *
     * @var array
     */
    protected $AddIndexes = array();

    /**
     * Delete table indexes operation
     *
     * @var array
     */
    protected $DeleteIndexes = array();

    /**
     * Process add indexes
     *
     * @return void
     */
    protected function processAddIndexes()
    {
        foreach ($this->AddIndexes as $TableName => $Single) {
            foreach ($Single as $KeyType => $Columns) {
                $KeyType = static::sw_index($KeyType, true);
                $TableColumns = implode("`), ADD {$KeyType}(`", $Columns);

                $QueryString = <<<SQL
ALTER TABLE `{$TableName}` ADD {$KeyType}(`{$TableColumns}`);
SQL;
                $this->query($QueryString);
            }
        }
    }

    /**
     * Process add indexes on add table
     *
     * @param string $table
     * @return string|void
     */
    protected function processAddIndexesOnAddTable(string $table)
    {
        if ($TableIndexArr = rec_arr_val($this->AddIndexes, $table)) {
            $Sql = ",\n";

            foreach ($TableIndexArr as $KeyType => $ColumnArr) {
                $KeyType = static::sw_index($KeyType, true);
                foreach ($ColumnArr as $Column) {
                    $Sql .= "  {$KeyType} KEY (`{$Column}`) ,\n";
                }
            }

            unset($this->AddIndexes[$table]);
            return rtrim($Sql, ",\n");
        }
    }

    /**
     * Process delete indexes
     *
     * @return void
     */
    protected function processDeleteIndexes()
    {
        foreach ($this->DeleteIndexes as $TableName => $Single) {
            foreach ($Single as $KeyType => $Columns) {
                if ($KeyType != "PRI") {
                    $TableColumns = implode("`, DROP INDEX `", $Columns);

                    $QueryString = <<<SQL
ALTER TABLE `{$TableName}` DROP INDEX `{$TableColumns}`;
SQL;
                } else {
                    $QueryString = <<<SQL
ALTER TABLE `{$TableName}` DROP PRIMARY KEY;
SQL;
                }
                $this->query($QueryString);
            }
        }
    }

    /**
     * Switch index
     *
     * @param string $index
     * @param boolean $rev true if full value (Eg: PRIMARY) return
     * @return string
     */
    private static function sw_index(string $index, $rev = false): string
    {
        $ca = array(
            "PRIMARY" => "PRI",
            "UNIQUE" => "UNI"
        );

        $rev && $ca = array_flip($ca);
        return rec_arr_val($ca, $index);
    }
}