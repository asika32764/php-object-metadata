<?php

declare(strict_types=1);

namespace Asika\ObjectMetadata;

class ObjectMetadata
{
    protected static array $instances = [];

    protected WeakObjectStorage $storage;

    public function __construct(?WeakObjectStorage $storage = null)
    {
        $this->storage = $storage ?? new WeakObjectStorage();
    }

    public static function getInstance(string $scope): static
    {
        return static::$instances[$scope] ??= new static();
    }

    public static function getAllInstances(): array
    {
        return static::$instances;
    }

    public static function restInstances(): void
    {
        static::$instances = [];
    }

    public function get(object $object, string $key): mixed
    {
        return $this->getMetadata($object)[$key] ?? null;
    }

    public function set(object $object, string $key, mixed $value): static
    {
        $metadata = $this->getMetadata($object);

        $metadata[$key] = $value;

        $this->setMetadata($object, $metadata);

        return $this;
    }

    public function remove(object $object, string $key): static
    {
        $metadata = $this->getMetadata($object);

        unset($metadata[$key]);

        $this->setMetadata($object, $metadata);

        return $this;
    }

    public function has(object $object, string $key): bool
    {
        $metadata = $this->getMetadata($object);

        return isset($metadata[$key]);
    }

    public function getMetadata(object $object): array
    {
        return $this->storage->get($object) ?? [];
    }

    public function setMetadata(object $object, array $data): static
    {
        $this->storage->set($object, $data);

        return $this;
    }

    public function removeMetadata(object $object): static
    {
        $this->storage->remove($object);

        return $this;
    }

    public function hasMetadata(object $object): bool
    {
        return $this->storage->has($object);
    }

    public function getStorage(): WeakObjectStorage
    {
        return $this->storage;
    }

    public function wrapper(object $item): MetadataProxy
    {
        return new MetadataProxy($item, $this);
    }
}
