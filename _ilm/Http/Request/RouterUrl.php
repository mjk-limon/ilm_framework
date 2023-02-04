<?php

namespace _ilmComm\Core\Http\Request;

class RouterUrl extends Url
{
    public function initQuerySettings()
    {
        $qParArr = $this->getQueryArray();

        if (array_key_exists("cur", $qParArr) || array_key_exists("lang", $qParArr)) {
            $key = isset($qParArr['cur']) ? 'cur' : 'lang';
            setcookie('_ilm_pset_' . $key, $qParArr[$key], time() + 3600, "/");

            $this->setQueryParameter($key, null);
            redirect($this->write(
                self::WRITE_FLAG_OMIT_SCHEME |
                    self::WRITE_FLAG_OMIT_HOST
            ));
        }
    }
}
