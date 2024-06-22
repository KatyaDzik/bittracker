<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PasswordMatchValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var $constraint PasswordMatch */
        if ($value !== $this->context->getRoot()->get('password')->getData()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}