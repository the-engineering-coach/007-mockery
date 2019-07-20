<?php
declare(strict_types=1);

namespace Braddle;

use Psr\Log\LoggerInterface;

class DrivingLicenceGenerator
{
    private $logger;
    private $randomNumbersGenerator;

    public function __construct(
        LoggerInterface $logger,
        RandomNumbersGenerator $randomNumbersGenerator
    ) {
        $this->logger = $logger;
        $this->randomNumbersGenerator = $randomNumbersGenerator;
    }

    public function generateNumber(LicenceApplicant $applicant)
    {
        if ($applicant->getAge() < 17) {
            $this->logger->notice("Under age application user: " . $applicant->getId());
            throw new InvalidDriverException(
                "Applicant is too young"
            );
        }

        if ($applicant->holdsLicence()) {
            $this->logger->notice("duplicate application user: " . $applicant->getId());
            throw new InvalidDriverException(
                "Cannot hold more than one licence"
            );
        }

        $licence = $applicant->getInitials() .
            $applicant->getDateOfBirth()->format("dmY");

        $numberOfDigits = 15 - strlen($licence);
        $numberOfDigits = ($numberOfDigits < 4) ? 4 : $numberOfDigits;

        return $licence . $this->randomNumbersGenerator->generate($numberOfDigits);
    }
}
