<?php

namespace App\Enum;

enum TorrentFileStatusEnum
{
    case Unverified;

    public function getValue(): ?int
    {
        return $this->name == TorrentFileStatusEnum::Unverified->name ? 0 : null;
    }
}
