<?php

declare(strict_types=1);

namespace Asika\ObjectMetadata\Test;

use Asika\ObjectMetadata\ObjectMetadata;
use PHPUnit\Framework\TestCase;

class ObjectMetadataTest extends TestCase
{
    protected function setUp(): void
    {
        ObjectMetadata::restInstances();
    }

    public function getMetadata(string $name = 'main'): ObjectMetadata
    {
        return ObjectMetadata::getInstance($name);
    }

    /**
     * @see  ObjectMetadata::get
     */
    public function testGetAndSet(): void
    {
        $item = new \stdClass();

        $meta = $this->getMetadata();

        $meta->set($item, 'foo', 'Hello');

        self::assertEquals(
            'Hello',
            $meta->get($item, 'foo')
        );

        self::assertEquals(
            'Hello',
            ObjectMetadata::getInstance('main')->get($item, 'foo')
        );

        self::assertEquals(
            'Hello',
            $meta->wrapper($item)->get('foo')
        );

        $item2 = clone $item;

        self::assertNull($meta->get($item2, 'foo'));

        $storage = $meta->getStorage();

        self::assertCount(
            1,
            $storage
        );

        unset($item);

        self::assertCount(
            0,
            $storage
        );
    }

    /**
     * @see  ObjectMetadata::remove
     */
    public function testRemove(): void
    {
        $meta = $this->getMetadata();

        $item = new \stdClass();

        $meta->set($item, 'foo', 'Hello');

        self::assertCount(
            1,
            $meta->getMetadata($item)
        );

        $meta->remove($item, 'foo');

        self::assertCount(
            0,
            $meta->getMetadata($item)
        );
    }

    /**
     * @see  ObjectMetadata::has
     */
    public function testHas(): void
    {
        $meta = $this->getMetadata();

        $item = new \stdClass();

        self::assertFalse($meta->has($item, 'foo'));

        $meta->set($item, 'foo', 'Hello');

        self::assertTrue($meta->has($item, 'foo'));
    }

    /**
     * @see  ObjectMetadata::hasMetadata
     */
    public function testHasMetadata(): void
    {
        $meta = $this->getMetadata();
        $item = new \stdClass();

        self::assertFalse($meta->hasMetadata($item));
    }

    /**
     * @see  ObjectMetadata::setMetadata
     */
    public function testGetAndSetMetadata(): void
    {
        $meta = $this->getMetadata();
        $item = new \stdClass();

        $meta->setMetadata(
            $item,
            [
                'foo' => 'Hello',
                'bar' => 'World'
            ]
        );

        self::assertCount(2, $meta->getMetadata($item));
        self::assertEquals('World', $meta->get($item, 'bar'));
    }

    /**
     * @see  ObjectMetadata::removeMetadata
     */
    public function testRemoveMetadata(): void
    {
        $meta = $this->getMetadata();
        $item = new \stdClass();

        $meta->setMetadata(
            $item,
            [
                'foo' => 'Hello',
                'bar' => 'World'
            ]
        );
        self::assertTrue($meta->hasMetadata($item));

        $meta->removeMetadata($item);
        self::assertFalse($meta->hasMetadata($item));
    }
}
