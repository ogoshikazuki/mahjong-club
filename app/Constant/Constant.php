<?php

namespace App\Constant;

use ReflectionClass;

abstract class Constant
{
    public static function getConstants()
    {
        $reflectionClass = new ReflectionClass(static::class);

        return $reflectionClass->getConstants();
    }
}
