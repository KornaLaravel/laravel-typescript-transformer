<?php

namespace Spatie\LaravelTypeScriptTransformer\Tests\FakeClasses;

use Spatie\LaravelData\DataCollection;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @extends DataCollection<TKey, TValue>
 */
class CustomDataCollection extends DataCollection
{
}
