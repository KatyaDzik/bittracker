<?php

namespace App\Codec;

use App\Exception\TorrentException;

class EncodeTorrent implements EncodeTorrentInterface
{
    /**
     * @throws TorrentException
     */
    public function encode($mixed): string
    {
        return match (gettype($mixed)) {
            is_null($mixed) => $this->encodeString(''),
            'string' => $this->encodeString($mixed),
            'integer', 'double' => $this->encodeInt(sprintf('%.0f', round($mixed))),
            'array' => $this->encodeArray($mixed),
            default => throw new TorrentException('Unsupported type: ' . gettype($mixed)),
        };
    }

    protected function encodeString($str): string
    {
        return strlen($str) . ':' . $str;
    }

    protected function encodeInt($int): string
    {
        return 'i' . $int . 'e';
    }

    /**
     * @throws TorrentException
     */
    protected function encodeArray(array $array): string
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
        } else {
            // We build a Dictionary
            ksort($array, SORT_STRING);
            $return = 'd';
            foreach ($array as $key => $val) {
                $return .= $this->encode(strval($key));
                $return .= $this->encode($val);
            }
        }
        $return .= 'e';

        return $return;
    }
}