<?php

namespace _ilmComm\Core\DataBase;

use _ilmComm\DataBase;
use _ilmComm\FormHandler;

class DBQuery
{
	public static function getSome($tn, $exsql, $index = '*')
	{
		$db = new DataBase;
		$db->query($tn, "WHERE " . $exsql, $index);
		return $db->getData();
	}

	public static function getSingle($tn, $exsql, $index = '*')
	{
		$db = new DataBase;
		$db->query($tn, "WHERE " . $exsql, $index);
		return $db->getData(true);
	}

	public static function getSingleIndex($tn, $exsql, $index = '*')
	{
		$db = new DataBase;
		$db->query($tn, "WHERE " . $exsql, $index);
		return $db->getData(true, $index);
	}

	public static function applyMath($tn, $cond, $func)
	{
		$db = new DataBase;
		$res = $db->query("SELECT {$func} AS r FROM {$tn} WHERE {$cond}")->fetch_assoc();
		return rec_arr_val($res, 'r');
	}

	public static function retrieveArray($tn, $exsql, $index = '*')
	{
		$db = new DataBase;
		$col = ($index == "*") ? null : $index;
		$db->query($tn, "WHERE " . $exsql, $index);
		return $db->retrieveArray($col);
	}

	public static function updateStatus($tn, $col, $val, $cond)
	{
		$fh = new FormHandler;
		$fh->setTableName($tn);
		$fh->initFormData(array(
			$col => $val
		));

		return $fh->updateTable($cond);
	}
}
