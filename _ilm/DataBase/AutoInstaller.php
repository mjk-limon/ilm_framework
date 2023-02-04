<?php

namespace _ilmComm\Core\DataBase;

use _ilmComm\Core\DataBase\Traits\TableColumnOperation;
use _ilmComm\Core\DataBase\Traits\TableIndexOperation;
use _ilmComm\Core\DataBase\Traits\TableOperation;
use _ilmComm\Core\DataBase\Traits\TableSeeders;
use _ilmComm\Exceptions\DatabaseException;

class AutoInstaller extends DataBaseWithIUD_FetchOperation
{
    use TableOperation,
        TableColumnOperation,
        TableIndexOperation,
        TableSeeders;

    /**
     * Table structure array
     *
     * @var array
     */
    protected $TableStructure = array();

    /**
     * Table seeders array
     *
     * @var array
     */
    protected $TableSeeders = array();

    /**
     * All columns array
     *
     * @var array
     */
    protected $AllColumnsArr = array();

    /**
     * Autoinstaller constructor
     */
    public function __construct()
    {
        $this->TableStructure = include doc_root("admin/install/db/structure.php");
        $this->TableSeeders = include doc_root("admin/install/db/seeders.php");
        parent::__construct();
    }

    /**
     * Set table structure
     *
     * @param array $t
     * @return void
     */
    public function setTableStructure(array $t)
    {
        $this->TableStructure = $t;
    }

    /**
     * Set table seeders
     *
     * @param array $s
     * @return void
     */
    public function setTableSeeders(array $s)
    {
        $this->TableSeeders = $s;
    }

    /**
     * Init installer
     *
     * @return bool|DatabaseException
     */
    public function init()
    {
        // Build properties
        $this->parseTableStructure();

        try {
            // Process table operation
            $this->processAddTable();
            $this->processDeleteTable();

            // Process column operation
            $this->processAddColumns();
            $this->processChangeColumns();
            $this->processDeleteColumns();

            // Process index operation
            $this->processAddIndexes();
            $this->processDeleteIndexes();

            // Init tabble seeders
            $this->processSeeders();
        } catch (DatabaseException $e) {
            echo '<pre>'; print_r($e); echo '</pre>';
            return false;
        }

        return true;
    }

    /**
     * Parse table structure array and build info
     *
     * @return void
     */
    protected function parseTableStructure()
    {
        foreach ($this->TableStructure as $TableName => $TableInfoArr) {
            $CurrentTableStructure = array();
            $ResultCurrTblScrutecture = $this->query("DESCRIBE `{$TableName}`");

            if ($ResultCurrTblScrutecture->checkResult()) {
                // Table exists
                // Table structure to php array
                while ($CurrentTableStructure[] = $ResultCurrTblScrutecture->fetch_assoc()) {
                }
            } else {
                // Table not exists
                // Table name pushed to add table array
                $this->AddTables[] = $TableName;
            }

            foreach ($TableInfoArr as $ColumnName => $ColumnInfo) {
                // Parse variable from column info
                list($DataType, $CharType, $Index, $Extra) = array_replace(
                    array("int(11)", "NOT NULL", "", ""),
                    $ColumnInfo
                );

                // Create statement
                $ColumnStamtString = "`$ColumnName` {$DataType} {$CharType} {$Extra} ";
                $this->AllColumnsArr[$TableName][] = $ColumnStamtString;

                // Check current column
                $CurrentColumnKey = array_search($ColumnName, array_column($CurrentTableStructure, "Field"));

                if (static::sw_index($Index) != rec_arr_val($CurrentTableStructure, "{$CurrentColumnKey},Key")) {
                    // 
                    if (!empty($Index)) {
                        $this->AddIndexes[$TableName][static::sw_index($Index)][] = $ColumnName;
                    }

                    if ($DeleteIndex = rec_arr_val($CurrentTableStructure, "{$CurrentColumnKey},Key")) {
                        $this->DeleteIndexes[$TableName][$DeleteIndex][] = $ColumnName;
                    }
                }

                if (array_search($TableName, $this->AddTables) !== false) {
                    // Table already added to addtable array
                    continue;
                }

                if ($CurrentColumnKey === false) {
                    // Column not exists
                    if (isset($LastColumn)) {
                        $Position = "AFTER `{$LastColumn}`";
                    } else {
                        $Position = "FIRST";
                    }

                    // Push column to addcolumns array
                    $this->AddColumns[$TableName][] = $ColumnStamtString . $Position;
                } else {
                    // Column exists
                    // Push column to changecolumns array
                    $this->ChangeColumns[$TableName][$ColumnName] = $ColumnStamtString;
                }

                // Asign last column
                $LastColumn = $ColumnName;
            }

            if ($Diffs = array_diff(array_column($CurrentTableStructure, "Field"), array_keys($this->TableStructure[$TableName]))) {
                // Extra column found in current structure
                // Push difference to deletecolumns array
                $this->DeleteColumns[$TableName] = $Diffs;
            }
        }

        // Current tables
        $CurrentTables = array();
        $R = $this->query("SHOW TABLES");
        while ($Tbl = $R->fetch_assoc()) {
            $CurrentTables[] = current($Tbl);
        }
        // Push table difference to deletetable array
        $this->DeleteTables = array_diff($CurrentTables, array_keys($this->TableStructure));
    }
}
