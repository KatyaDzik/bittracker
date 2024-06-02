<?php

namespace App\Service;

class Encode
{
    public function encode($mixed)
    {
        switch (gettype($mixed)) {
            case is_null($mixed):
                return $this->encodeString('');
            case 'string':
                return $this->encodeString($mixed);
            case 'integer':
            case 'double':
                return $this->encodeInt(sprintf('%.0f', round($mixed)));
            case 'array':
                return $this->encodeArray($mixed);
        }
    }

    function encodeString($str)
    {
        return strlen($str) . ':' . $str;
    }

    function encodeInt($int)
    {
        return 'i' . $int . 'e';
    }

    function encodeArray(array $array)
    {
        // Check for strings in the keys
        $isList = true;
        foreach (array_keys($array) as $key) {
            if (!is_int($key)) {
                $isList = false;
                break;
            }
        }

        if ($isList) {
            // Wie build a list
            ksort($array, SORT_NUMERIC);
            $return = 'l';
            foreach ($array as $val) {
                $return .= $this->encode($val);
            }
            $return .= 'e';
        } else {
            // We build a Dictionary
            ksort($array, SORT_STRING);
            $return = 'd';
            foreach ($array as $key => $val) {
                $return .= $this->encode(strval($key));
                $return .= $this->encode($val);
            }
            $return .= 'e';
        }

        return $return;
    }
}