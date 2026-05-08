<?php

namespace Spatie\LaravelTypeScriptTransformer\Tests\FakeClasses;

use Spatie\LaravelData\CursorPaginatedDataCollection;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @extends CursorPaginatedDataCollection<TKey, TValue>
 */
class CustomCursorPaginatedDataCollection extends CursorPaginatedDataCollection
{
}
