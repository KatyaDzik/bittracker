<?php

namespace App\Codec;

use App\Dto\DecodedTorrentDataDto;
use Symfony\Component\HttpFoundation\File\File;

interface BecodeTorrentInterface
{
    public function becodeFile(File $file): DecodedTorrentDataDto;
}