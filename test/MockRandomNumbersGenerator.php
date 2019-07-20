<?php
declare(strict_types=1);
namespace Braddle\Test;

use Braddle\RandomNumbersGenerator;

class MockRandomNumbersGenerator implements RandomNumbersGenerator
{
    private $responses;

    public function mockGenerate(array $callAndRespond)
    {
        $this->responses = $callAndRespond;
    }

    public function generate(int $numberOfDigits): string
    {
        return $this->responses[$numberOfDigits];
    }
}
