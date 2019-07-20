<?php
declare(strict_types=1);

namespace Braddle\Test;

use Braddle\DrivingLicenceGenerator;
use Braddle\InvalidDriverException;
use PHPUnit\Framework\TestCase;

class DrivingLicenceGeneratorTest extends TestCase
{

    private $logger;

    protected function setUp()
    {
        parent::setUp();

        $this->logger = new SpyLogger();
        $this->random = new MockRandomNumbersGenerator();

        $this->generator = new DrivingLicenceGenerator(
            $this->logger,
            $this->random
        );
    }

    public function testUnderAgeApplicantCannotGenerateLicence()
    {
        $this->expectException(InvalidDriverException::class);
        $this->expectExceptionMessage("Applicant is too young");

        $applicant = new UnderAgeApplicant();

        $this->generator->generateNumber($applicant);
    }

    public function testUnderAgeApplicationsAreLogged()
    {
        $applicant = new UnderAgeApplicant();

        try {
            $this->generator->generateNumber($applicant);
        } catch (InvalidDriverException $e) {

        }

        $this->assertEquals(1, $this->logger->noticeCalledCount);
        $this->assertEquals("Under age application user: 123", $this->logger->noticeLastMessage);
    }

    public function testLicenceHolderCannotGenerateLicence()
    {
        $this->expectException(InvalidDriverException::class);
        $this->expectExceptionMessage("Cannot hold more than one licence");

        $applicant = new LicenceHolderApplicant();

        $this->generator->generateNumber($applicant);
    }

    public function testLicenceHolderAttemtsLogged()
    {
        $applicant = new LicenceHolderApplicant();

        try {
            $this->generator->generateNumber($applicant);
        } catch (InvalidDriverException $e) {

        }

        $this->assertEquals(1, $this->logger->noticeCalledCount);
        $this->assertEquals("duplicate application user: 123", $this->logger->noticeLastMessage);
    }

    public function testValidApplicantCanGenerateLicence()
    {
        $applicant = new ValidApplicant("MDB");

        $this->random->mockGenerate([4 => "0123"]);

        $licenceNumber = $this->generator->generateNumber($applicant);

        $this->assertEquals(
            "MDB110719990123",
            $licenceNumber
        );
    }

    public function testLicenceNumberAreAtleast15Characters()
    {
        $applicant1 = new ValidApplicant("M");
        $applicant2 = new ValidApplicant("MD");
        $applicant4 = new ValidApplicant("MDBF");

        $this->random->mockGenerate(
            [
                4 => "0123",
                5 => "01234",
                6 => "012345",
            ]
        );

        $this->assertEquals(
            "M11071999012345",
            $this->generator->generateNumber($applicant1)
        );

        $this->assertEquals(
            "MD1107199901234",
            $this->generator->generateNumber($applicant2)
        );

        $this->assertEquals(
            "MDBF110719990123",
            $this->generator->generateNumber($applicant4)
        );
    }
}
