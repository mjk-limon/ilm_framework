<?php

namespace _ilmComm\Core\DataBase;

use _ilmComm\Core\DataBase\Traits\FetchOperation;

class DataBaseWithFetchOperation extends DataBase
{
    use FetchOperation;
}