<?php
declare(strict_types=1);

namespace Braddle\Test;

use Braddle\LicenceApplicant;

class UnderAgeApplicant implements LicenceApplicant
{
    public function getAge(): int
    {
        return 16;
    }

    public function holdsLicence(): bool
    {
        return false;
    }

    public function getId(): int
    {
        return 123;
    }

    public function getDateOfBirth(): \DateTime
    {
        return new \DateTime();
    }

    public function getInitials(): string
    {
        return "ABC";
    }
}
