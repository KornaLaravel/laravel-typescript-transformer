<?php

namespace Spatie\LaravelTypeScriptTransformer\LaravelData\ClassPropertyProcessors;

use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use Spatie\LaravelData\Attributes\Hidden as DataHidden;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Support\DataConfig;
use Spatie\TypeScriptTransformer\Attributes\Hidden;
use Spatie\TypeScriptTransformer\PhpNodes\PhpPropertyNode;
use Spatie\TypeScriptTransformer\References\ClassStringReference;
use Spatie\TypeScriptTransformer\Transformers\ClassPropertyProcessors\ClassPropertyProcessor;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptIdentifier;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptNull;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptProperty;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptReference;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptUnion;

class DataClassPropertyProcessor implements ClassPropertyProcessor
{
    protected array $lazyTypes = [
        'Spatie\LaravelData\Lazy',
        'Spatie\LaravelData\Support\Lazy\ClosureLazy',
        'Spatie\LaravelData\Support\Lazy\ConditionalLazy',
        'Spatie\LaravelData\Support\Lazy\DefaultLazy',
        'Spatie\LaravelData\Support\Lazy\InertiaDeferred',
        'Spatie\LaravelData\Support\Lazy\InertiaLazy',
        'Spatie\LaravelData\Support\Lazy\LivewireLostLazy',
        'Spatie\LaravelData\Support\Lazy\RelationalLazy',
    ];

    public function __construct(
        protected DataConfig $dataConfig,
        protected array $customLazyTypes = [],
        protected bool $nullableAsOptional = false,
    ) {
        $this->lazyTypes = array_merge($this->lazyTypes, $this->customLazyTypes);
    }

    public function execute(
        PhpPropertyNode $phpPropertyNode,
        ?TypeNode $annotation,
        TypeScriptProperty $property
    ): ?TypeScriptProperty {
        if (! empty($phpPropertyNode->getAttributes(Hidden::class)) && ! empty($phpPropertyNode->getAttributes(DataHidden::class))) {
            return null;
        }

        $outputMappedName = $this->resolveOutputMappedName($phpPropertyNode);

        if ($outputMappedName !== null) {
            $property->name = new TypeScriptIdentifier($outputMappedName);
        }

        if (! $property->type instanceof TypeScriptUnion) {
            return $property;
        }

        foreach ($property->type->types as $i => $subType) {
            if ($subType instanceof TypeScriptReference && $this->shouldHideReference($subType)) {
                $property->isOptional = true;

                unset($property->type->types[$i]);
            }

            if ($this->nullableAsOptional && $subType instanceof TypeScriptNull) {
                $property->isOptional = true;

                unset($property->type->types[$i]);
            }
        }

        $property->type->types = array_values($property->type->types);

        if (count($property->type->types) === 1) {
            $property->type = $property->type->types[0];
        }

        return $property;
    }

    protected function resolveOutputMappedName(PhpPropertyNode $phpPropertyNode): string|int|null
    {
        $className = $phpPropertyNode->getDeclaringClass()->reflection->getName();
        $propertyName = $phpPropertyNode->getName();

        $dataProperty = $this->dataConfig->getDataClass($className)->properties[$propertyName] ?? null;

        return $dataProperty?->outputMappedName;
    }

    protected function shouldHideReference(
        TypeScriptReference $reference
    ): bool {
        if (! $reference->reference instanceof ClassStringReference) {
            return false;
        }

        return in_array($reference->reference->classString, $this->lazyTypes)
            || $reference->reference->classString === Optional::class;
    }
}
