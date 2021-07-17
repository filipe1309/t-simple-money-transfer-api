<?php

namespace App\Helpers;

class OrderByHelper
{
    public static function treatOrderBy(string $orderBy): array
    {
        $orderByArray = [];

        if (empty($orderBy)) {
            return $orderByArray;
        }

        foreach (explode(',', $orderBy) as $value) {
            $value = trim($value);

            if (!preg_match("/^(-)?[A-Za-z0-9_]+$/", $value)) {
                throw new \InvalidArgumentException('"order_by" param is in invalid format');
            }

            $orderByArray[$value] = 'ASC';

            if (strstr($value, '-')) {
                $orderByArray[$value] = 'DESC';
            }
        }

        return $orderByArray;
    }
}
