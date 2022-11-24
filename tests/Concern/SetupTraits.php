<?php

declare(strict_types=1);

namespace App\Tests\Concern;

use ReflectionClass;
use ReflectionMethod;

trait SetupTraits
{
    abstract protected function classUsesRecursive(string $class): array;

    protected function setUpTraits(): void
    {
        $traits = array_keys($this->classUsesRecursive(static::class));

        foreach ($traits as $trait) {
            $reflection = new ReflectionClass($trait);

            if (! str_starts_with($reflection->getShortName(), 'With')) {
                continue;
            }

            $methods = $reflection->getMethods(ReflectionMethod::IS_PROTECTED);
            foreach ($methods as $method) {
                if (str_starts_with($method->name, 'setUp')) {
                    $this->{$method->name}();
                }
            }
        }
    }
}
