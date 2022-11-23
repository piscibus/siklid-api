<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\ODM\MongoDB\Types\Type;
use ReflectionClass;
use ReflectionMethod;

/**
 * Class TestCase
 * All Unit tests should extend this class.
 *
 * @psalm-suppress MissingConstructor
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpTraits();
    }

    protected function setUpTraits(): void
    {
        $traits = array_keys($this->classUsesDeep(static::class));

        foreach ($traits as $trait) {
            $reflection = new ReflectionClass($trait);
            $methods = $reflection->getMethods(ReflectionMethod::IS_PROTECTED);

            foreach ($methods as $method) {
                if (str_starts_with($method->name, 'setUp')) {
                    $this->{$method->name}();
                }
            }
        }
    }

    /**
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     *
     * @return array<class-string, class-string>
     */
    protected function classUsesDeep(string $class): array
    {
        $traits = (array)class_uses($class);

        $parents = (array)class_parents($class);

        foreach ($parents as $parent) {
            assert(is_string($parent));
            $traits += $this->classUsesDeep($parent);
        }

        return array_unique($traits);
    }

    /**
     * Creates an instance of the given mongo-odm custom type.
     *
     * @param class-string<T|Type> $type the type to create
     *
     * @return T
     *
     * @template T|Type
     */
    protected function createMongoType(string $type): Type
    {
        $type::registerType($type, $type);

        return $type::getType($type);
    }
}
