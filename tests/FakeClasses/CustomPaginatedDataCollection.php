<?php

namespace Spatie\LaravelTypeScriptTransformer\Tests\FakeClasses;

use Spatie\LaravelData\PaginatedDataCollection;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @extends PaginatedDataCollection<TKey, TValue>
 */
class CustomPaginatedDataCollection extends PaginatedDataCollection
{
}
