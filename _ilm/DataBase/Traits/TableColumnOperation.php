<?php

namespace _ilmComm\Core\DataBase\Traits;

trait TableColumnOperation
{
    /**
     * Add Columns array
     *
     * @var array
     */
    protected $AddColumns = array();

    /**
     * Change Column array
     *
     * @var array
     */
    protected $ChangeColumns = array();

    /**
     * Delete Column array
     *
     * @var array
     */
    protected $DeleteColumns = array();

    /**
     * Process add columns
     *
     * @return void
     */
    protected function processAddColumns()
    {
        foreach ($this->AddColumns as $TableName => $Single) {
            $TableColumns = implode(", ADD ", $Single);

            $QueryString = <<<SQL
ALTER TABLE `{$TableName}` ADD {$TableColumns};
SQL;
            $this->query($QueryString);
        }
    }

    /**
     * Process change columns
     *
     * @return void
     */
    protected function processChangeColumns()
    {
        foreach ($this->ChangeColumns as $TableName => $Single) {
            $TableColumns = implode(", CHANGE ", array_map(function ($s, $c) {
                return "`{$c}` {$s}";
            }, $Single, array_keys($Single)));

            $QueryString = <<<SQL
ALTER TABLE `{$TableName}` CHANGE {$TableColumns};
SQL;
            $this->query($QueryString);
        }
    }

    /**
     * Process delete columns
     *
     * @return void
     */
    protected function processDeleteColumns()
    {
        foreach ($this->DeleteColumns as $TableName => $Single) {
            $TableColumns = implode("`, DROP `", $Single);

            $QueryString = <<<SQL
ALTER TABLE `{$TableName}` DROP `{$TableColumns}`;
SQL;
            $this->query($QueryString);
        }
    }
}