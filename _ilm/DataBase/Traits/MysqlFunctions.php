<?php

namespace _ilmComm\Core\DataBase\Traits;

use _ilmComm\Exceptions\DatabaseException;

trait MysqlFunctions
{
    public function interval($diff, string $func = 'NOW()'): string
    {
        $types = ['s' => 'second', 'm' => 'minute', 'h' => 'hour', 'd' => 'day', 'M' => 'month', 'Y' => 'year'];
        $incr = '+';
        $items = '';
        $type = 'd';

        if ($diff && preg_match('/([+-]?) ?([0-9]+) ?([a-zA-Z]?)/', $diff, $matches)) {
            if (!empty($matches[1])) {
                $incr = $matches[1];
            }

            if (!empty($matches[2])) {
                $items = $matches[2];
            }

            if (!empty($matches[3])) {
                $type = $matches[3];
            }

            if (!in_array($type, array_keys($types))) {
                throw DatabaseException::create(6, $diff);
            }

            $func .= ' ' . $incr . ' interval ' . $items . ' ' . $types[$type] . ' ';
        }

        return $func;
    }

    public function now($diff = null, string $func = 'NOW()'): array
    {
        return ['[F]' => [$this->interval($diff, $func)]];
    }

    public function inc(int $num = 1): array
    {
        return ['[I]' => '+' . $num];
    }

    public function dec(int $num = 1): array
    {
        return ['[I]' => '-' . $num];
    }

    public function not($col = null): array
    {
        return ['[N]' => (string) $col];
    }

    public function func($expr, $bindParams = null): array
    {
        return ['[F]' => [$expr, $bindParams]];
    }
}
