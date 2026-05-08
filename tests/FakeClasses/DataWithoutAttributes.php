<?php

namespace Spatie\LaravelTypeScriptTransformer\Tests\FakeClasses;

use Spatie\LaravelData\Data;

class DataWithoutAttributes extends Data
{
    public function __construct(
        public string $firstName,
        public string $lastName,
    ) {
    }
}
