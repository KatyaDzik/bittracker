<?php

namespace App\Codec;

interface EncodeTorrentInterface
{
    public function encode($mixed): string;
}