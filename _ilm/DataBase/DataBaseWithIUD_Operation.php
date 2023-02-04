<?php

namespace _ilmComm\Core\DataBase;

use _ilmComm\Core\DataBase\Traits\InsertAndReplaceOperation;
use _ilmComm\Core\DataBase\Traits\IUD_OperationHelper;
use _ilmComm\Core\DataBase\Traits\UpdateAndDeleteOperation;

class DataBaseWithIUD_Operation extends DataBase
{
    use IUD_OperationHelper,
        InsertAndReplaceOperation,
        UpdateAndDeleteOperation;
}
