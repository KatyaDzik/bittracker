<?php

namespace App\Dto;

class AnnounceOutputDto
{
    public function __construct(
        private readonly int $leechers,
        private readonly int $seeders,
    ) {
    }

    public function getLeechers(): int
    {
        return $this->leechers;
    }

    public function getSeeders(): int
    {
        return $this->seeders;
    }
}