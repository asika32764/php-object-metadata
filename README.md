# PHP Object Metadata

<p>
    <img alt="GitHub" src="https://img.shields.io/github/license/asika32764/php-object-metadata?style=flat-square">
    <img alt="GitHub Workflow Status" src="https://img.shields.io/github/actions/workflow/status/asika32764/php-object-metadata/test.yml?label=test&style=flat-square">
    <img alt="Packagist Downloads" src="https://img.shields.io/packagist/dt/asika/object-metadata?style=flat-square">
    <a href="https://packagist.org/packages/asika/object-metadata">
        <img alt="Packagist Version" src="https://img.shields.io/packagist/v/asika/object-metadata?style=flat-square">
    </a>
</p>

`Object Metadata` is a package to help developer manage custom global object metadata.
This package use `WeakMap` to control the data mapping with object instances.

## Installation

```shell
composer require asika/object-metadata
```

## Getting Started

Basic usage:

```php
use Asika\ObjectMetadata\ObjectMetadata;

// Create any objects
$obj = new ArticleEntity();

// Get global main instance
$meta = ObjectMetadata::getInstance();

// Set custom metadata
$meta->set($obj, 'foo', 'Hello');

// Now you can get the data everywhere if this object still exists and not destruct yet
ObjectMetadata::getInstance()->get($obj, 'foo'); // Hello

// Available methods
$meta->get($obj, 'key');
$meta->set($obj, 'key', 'value');
$meta->has($obj, 'key');
$meta->remove($obj, 'key');
$meta->getMetadata($obj, 'key'); // array
$meta->setMetadata($obj, 'key', $data);

```

Use wrapper:

```php
$obj = new ArticleEntity();
$meta = ObjectMetadata::getInstance();

$metaWrapper = $meta->wrapper($obj);
$metaWrapper->set('foo', 'Hello');

$metaWrapper->get('key');
$metaWrapper->has('key');
$metaWrapper->remove('key');
$metaWrapper->all();

// Array Access
$metaWrapper['key'] = 'value';

// If object destructed, getting metadata will be NULL
unset($obj);

$metaWrapper->get('foo'); // NULL
```

## Scope

The `ObjectMetadata` is able to separate different scopes.

```php
$meta = ObjectMetadata::getInstance('main'); // Main scope

$appMeta = ObjectMetadata::getInstance('app');

$dbMeta = ObjectMetadata::getInstance('db');
```

## What is The Real Usage

The useful case for this package is that we can make some entity object or value object to be a
rich object. for example, if a ORM uses data-mapper pattern, their entity object will be a anemic object
which may not keep the ORM instance.

```php
$item = new Article();

$item = $orm->createOne($item);

// This item will only contains pure data
```

If the ORM use this package to make themselves as entity metadata, we can make the `Article` entity as a
rich object and has the capacity to all ORM to get another objects.

```php
$orm->on('entity.prepare', function (object $entity, ORM $orm) {
    ObjectMetadata::getInstance('db')->set($entity, 'orm', $orm);
});

class Article 
{
    // ...

    // Article can use ObjectMetadata to get ORM instance.
    public function getComments() {
        $orm = ObjectMetadata::getInstance('db')->get($this, 'orm');
        
        return $orm->from(Comment::class)
            ->where('article_id', $this->getId())
            ->all();
    }
}

// Now we can test it
$article = $orm->createEntity(Article::class);

$item = $orm->createOne($item);

// Article is able to call ORM to get another items from DB
$item->getComments();
$item->getAuthor();
$item->getTags();
```

