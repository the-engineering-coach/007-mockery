<?php
declare(strict_types=1);

namespace Braddle\Test;

use Braddle\LicenceApplicant;

class ValidApplicant implements LicenceApplicant
{

    private $initials;

    public function __construct(string $initials)
    {
        $this->initials = $initials;
    }

    public function getAge(): int
    {
        return 21;
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
        return new \DateTime("11-07-1999 00:00:00");
    }

    public function getInitials(): string
    {
        return $this->initials;
    }
}
