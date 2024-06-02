<?php

namespace App\Dto;

class AnnounceOutputDto
{
    public function __construct(
        private readonly int $action,
        private readonly int $transaction_id,
        private readonly int $interval,
        private readonly int $leechers,
        private readonly int $seeders,
    ) {
    }

    public function getAction(): int
    {
        return $this->action;
    }

    public function getTransactionId(): int
    {
        return $this->transaction_id;
    }

    public function getInterval(): int
    {
        return $this->interval;
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