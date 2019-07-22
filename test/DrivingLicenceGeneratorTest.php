<?php
declare(strict_types=1);

namespace Braddle\Test;

use Braddle\DrivingLicenceGenerator;
use Braddle\InvalidDriverException;
use Braddle\LicenceApplicant;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class DrivingLicenceGeneratorTest extends MockeryTestCase
{

    /**
     * @var Mock
     */
    private $logger;

    protected function setUp()
    {
        parent::setUp();

        $this->logger = \Mockery::spy(LoggerInterface::class);
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

        $this->generator->generateNumber($this->getUnderageApplicant());
    }

    public function testUnderAgeApplicationsAreLogged()
    {
        try {
            $this->generator->generateNumber($this->getUnderageApplicant());
        } catch (InvalidDriverException $e) {

        }

        $this->logger->shouldHaveReceived("notice")
            ->with("Under age application user: 123")
            ->once();
    }

    public function testLicenceHolderCannotGenerateLicence()
    {
        $this->expectException(InvalidDriverException::class);
        $this->expectExceptionMessage("Cannot hold more than one licence");

        $this->generator->generateNumber($this->getLicenceHolderApplicant());
    }

    public function testLicenceHolderAttemtsLogged()
    {
        try {
            $this->generator->generateNumber($this->getLicenceHolderApplicant());
        } catch (InvalidDriverException $e) {

        }

        $this->logger->shouldHaveReceived("notice")
            ->with("duplicate application user: 123")
            ->once();
    }

    public function testValidApplicantCanGenerateLicence()
    {
        $this->random->mockGenerate([4 => "0123"]);

        $licenceNumber = $this->generator->generateNumber($this->getValidApplicant("MDB"));

        $this->assertEquals(
            "MDB110719990123",
            $licenceNumber
        );
    }

    public function testLicenceNumberAreAtleast15Characters()
    {
        $applicant1 = $this->getValidApplicant("M");
        $applicant2 = $this->getValidApplicant("MD");
        $applicant4 = $this->getValidApplicant("MDBF");

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

    private function getUnderageApplicant()
    {
        $applicant = \Mockery::mock(LicenceApplicant::class);
        $applicant->shouldReceive("getAge")->andReturn(16);
        $applicant->shouldReceive("getId")->andReturn(123);
        return $applicant;
    }

    private function getLicenceHolderApplicant()
    {
        $applicant = \Mockery::mock(LicenceApplicant::class);
        $applicant->shouldReceive("getAge")->andReturn(18);
        $applicant->shouldReceive("holdsLicence")->andReturnTrue();
        $applicant->shouldReceive("getId")->andReturn(123);
        return $applicant;
    }

    private function getValidApplicant(string $initials)
    {
        $applicant = \Mockery::mock(LicenceApplicant::class);
        $applicant->shouldReceive("getAge")->andReturn(18);
        $applicant->shouldReceive("holdsLicence")->andReturnFalse();
        $applicant->shouldReceive("getId")->andReturn(123);
        $applicant->shouldReceive("getInitials")->andReturn($initials);
        $applicant->shouldReceive("getDateOfBirth")->andReturn(new \DateTime("11-07-1999 00:00:00"));
        return $applicant;
    }
}
