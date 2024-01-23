<?php

declare(strict_types=1);

namespace Asika\ObjectMetadata;

use Traversable;

final class WeakObjectStorage implements \IteratorAggregate, \Countable, \ArrayAccess
{
    protected static array $instances = [];

    protected \WeakMap $map;

    public function __construct()
    {
        $this->map = new \WeakMap();
    }

    public static function getInstance(string $name = 'main'): self
    {
        return self::$instances[$name] ??= new self();
    }

    public static function removeInstance(string $name = 'main'): void
    {
        unset(self::$instances[$name]);
    }

    public function get(object $item): mixed
    {
        return $this->getMap()[$item] ?? null;
    }

    public function set(object $item, mixed $value): static
    {
        $this->getMap()[$item] = $value;

        return $this;
    }

    public function remove(object $item): void
    {
        unset($this->getMap()[$item]);
    }

    public function has(object $item): bool
    {
        return isset($this->getMap()[$item]);
    }

    public function getMap(): \WeakMap
    {
        return $this->map;
    }

    public function count(): int
    {
        return count($this->getMap());
    }

    public function getIterator(): Traversable
    {
        foreach ($this->getMap() as $k => $v) {
            yield $k => $v;
        }
    }

    #[\ReturnTypeWillChange]
    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    #[\ReturnTypeWillChange]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }
}
