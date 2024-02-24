<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

class PasswordMatch extends Constraint
{
    public string $message = 'The password fields must match.';

    public function __construct(string $message = null, array $groups = null, $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
    }
}