<?php

namespace Braddle;

interface RandomNumbersGenerator
{
    public function generate(int $numberOfDigits) : string;
}
