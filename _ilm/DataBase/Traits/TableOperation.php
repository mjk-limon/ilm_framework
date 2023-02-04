<?php

namespace _ilmComm\Core\DataBase\Traits;

trait TableOperation
{
    /**
     * Add Tables array
     *
     * @var array
     */
    protected $AddTables = array();

    /**
     * Delete tables array
     *
     * @var array
     */
    protected $DeleteTables = array();

    /**
     * Process add table
     *
     * @return void
     */
    protected function processAddTable()
    {
        foreach ($this->AddTables as $Single) {
            $TableColumns = implode(",\n  ", $this->AllColumnsArr[$Single]);
            $TableKeys = static::processAddIndexesOnAddTable($Single);

            $QueryString1 = <<<SQL
DROP TABLE IF EXISTS `{$Single}`;
SQL;

            $QueryString2 = <<<SQL
CREATE TABLE IF NOT EXISTS `{$Single}` (
  {$TableColumns}{$TableKeys}
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SQL;
            $this->query($QueryString1);
            $this->query($QueryString2);
        }
    }

    /**
     * Process delete table
     *
     * @return void
     */
    protected function processDeleteTable()
    {
        foreach ($this->DeleteTables as $Single) {
            $QueryString = <<<SQL
DROP TABLE IF EXISTS `{$Single}`;
SQL;
            $this->query($QueryString);
        }
    }
}
