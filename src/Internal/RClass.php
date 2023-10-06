<?php

declare(strict_types=1);

namespace Square\Pjson\Internal;

use BackedEnum;
use ReflectionClass;
use Square\Pjson\FromJsonData;
use Square\Pjson\JsonSerialize;
use Square\Pjson\ToJsonData;
use UnitEnum;

class RClass
{
    protected static array $cache = [];

    protected ReflectionClass $rc;

    protected array $props;

    protected function __construct($class)
    {
        $this->rc = new ReflectionClass($class);
        $this->props = $this->rc->getProperties();

        foreach ($this->props as $prop) {
            $prop->setAccessible(true);
        }
    }

    public static function make($class): RClass
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if (! array_key_exists($class, self::$cache)) {
            self::$cache[$class] = new self($class);
        }

        return self::$cache[$class];
    }

    public function getProperties()
    {
        return $this->props;
    }

    public function source(): ReflectionClass
    {
        return $this->rc;
    }

    public function isBackedEnum(): bool
    {
        return interface_exists(BackedEnum::class) && $this->rc->implementsInterface(BackedEnum::class);
    }

    public function isEnum(): bool
    {
        return $this->rc->implementsInterface(UnitEnum::class);
    }

    public function isSimpleEnum(): bool
    {
        return $this->isEnum() && ! $this->isBackedEnum();
    }

    public function isMethodStatic(string $methodName): bool
    {
        return $this->rc->getMethod($methodName)->isStatic();
    }

    /**
     * True if the type either implements the FromJsonData interface or (in)directly uses the JsonSerialize trait
     */
    public function readsFromJson(): bool
    {
        $traits = $this->classUsesNestedTrait($this->rc->getName());

        return array_key_exists(JsonSerialize::class, $traits) || $this->rc->implementsInterface(FromJsonData::class);
    }

    /**
     * True if the type either implements the ToJsonData interface or (in)directly uses the JsonSerialize trait
     */
    public function writesToJson(): bool
    {
        $traits = $this->classUsesNestedTrait($this->rc->getName());

        return array_key_exists(JsonSerialize::class, $traits) || $this->rc->implementsInterface(ToJsonData::class);
    }

    /**
     * Gets the traits used by the current class, and also recursively any traits used by those traits
     */
    public function classUsesNestedTrait(string $class): array
    {
        $traits = class_uses($class);

        foreach ($traits as $trait) {
            $traits = array_merge($traits, $this->classUsesNestedTrait($trait));
        }

        return $traits;
    }
}
