<?php

namespace indigerd\embedded\helper;

class ArrayMerge
{
    public static function isAssoc($val) {
        if (!\is_array($val)) {
            return false;
        }
        $assoc = false;
        foreach ($val as $k => $v) {
            if (!\is_int($k)) {
                $assoc = true;
                break;
            }
        }
        return $assoc;
    }

    public static function mergeRecursive($array1, $array2)
    {
        foreach($array2 as $key=>$val) {
            if(isset($array1[$key])) {
                if(self::isAssoc($val)) {
                    $array1[$key] = self::mergeRecursive($array1[$key], $val);
                } else {
                    $array1[$key] = $val;
                }
            } else {
                $array1[$key] = $val;
            }
        }
        return $array1;
    }
}
