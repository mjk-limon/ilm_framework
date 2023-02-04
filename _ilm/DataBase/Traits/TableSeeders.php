<?php

namespace _ilmComm\Core\DataBase\Traits;

use _ilmComm\Core\DataBase\AutoInstaller;
use _ilmComm\Exceptions\DatabaseException;

trait TableSeeders
{
    protected function processSeeders()
    {
        /** @var AutoInstaller $this */

        foreach ($this->TableSeeders as $TableName => $TableValues) {
            try {
                $Result = $this->get($TableName);
                $DataNotExists = !$Result->num_rows && $TableValues;
            } catch (DatabaseException $e) {
                return false;
            }

            if ($DataNotExists) {
                $this->insertMulti($TableName, $TableValues);
            }
        }
    }
}
