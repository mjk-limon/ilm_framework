<?php

namespace _ilmComm\Core\DataBase;

use _ilmComm\Core\DataBase\Traits\FetchOperation;
use _ilmComm\Core\DataBase\Traits\InsertAndReplaceOperation;
use _ilmComm\Core\DataBase\Traits\IUD_OperationHelper;
use _ilmComm\Core\DataBase\Traits\UpdateAndDeleteOperation;

class DataBaseWithIUD_FetchOperation extends DataBase
{
    use FetchOperation,
        IUD_OperationHelper,
        InsertAndReplaceOperation,
        UpdateAndDeleteOperation;
}