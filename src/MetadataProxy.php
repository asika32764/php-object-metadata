<?php

declare(strict_types=1);

namespace Asika\ObjectMetadata;

class MetadataProxy
{
    protected \WeakReference $instance;

    public function __construct(object $instance, protected ObjectMetadata $metadata)
    {
        $this->instance = \WeakReference::create($instance);
    }

    public function get(string $key): mixed
    {
        if (!$this->instance->get()) {
            return null;
        }

        return $this->metadata->get($this->instance->get(), $key);
    }

    public function set(string $key, mixed $value): static
    {
        if (!$this->instance->get()) {
            return $this;
        }

        $this->metadata->set($this->instance->get(), $key, $value);

        return $this;
    }

    public function remove(string $key): static
    {
        if (!$this->instance->get()) {
            return $this;
        }

        $this->metadata->remove($this->instance->get(), $key);

        return $this;
    }

    public function has(string $key): bool
    {
        if (!$this->instance->get()) {
            return false;
        }

        return $this->metadata->has($this->instance->get(), $key);
    }

    public function all(): array
    {
        if (!$this->instance->get()) {
            return [];
        }

        return $this->metadata->getMetadata($this->instance->get());
    }

    public function getMetadataInstance(): ObjectMetadata
    {
        return $this->metadata;
    }
}
