<p align="center">
    <a href="https://github.com/saeedhosan/laravel-useful/actions"><img alt="GitHub Workflow Status (master)" src="https://img.shields.io/github/actions/workflow/status/saeedhosan/laravel-useful/tests.yml?branch=main&label=tests&style=round-square"></a>
    <a href="https://packagist.org/packages/saeedhosan/laravel-useful"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/saeedhosan/laravel-useful"></a>
    <a href="https://packagist.org/packages/saeedhosan/laravel-useful"><img alt="Latest Version" src="https://img.shields.io/packagist/v/saeedhosan/laravel-useful"></a>
    <a href="https://packagist.org/packages/saeedhosan/laravel-useful"><img alt="License" src="https://img.shields.io/github/license/saeedhosan/laravel-useful"></a>
</p>

# Laravel useful

This package provides Laravel Eloquent support, traits, and additional classes.

# Table of Contents

- [Introduction](#introduction)
- [Installation](#installation)
- [Model Traits](#model-traits)
    - [HasSlug](#hasslug)
    - [HasUuid](#hasuuid)
    - [HasRouteBinding](#HasRouteBinding)
    - [HasStaticAccess](#hasstaticaccess)
- [Eloquent](#eloquent)
    - [BelongsToOne](#belongsToOne)
- [Commands](#commands)
- [EnvEditor](#EnvEditor)
- [Support](./docs/support.md)

## Introduction

This package provide collection of reusable php classes to improve everyday Laravel development.

It provides **Eloquent model traits**, **utilities**, and **lightweight traits** that solve common problems - cleanly, safely, and in a Laravel-native way.

Each snippet is intentionally minimal, well-tested, and easy to drop into real-world projects.

## Installation

You can install the package via composer:

```bash
composer require saeedhosan/laravel-useful
```

## Model Tratis

The list of model trait that adds some functionalities to laravel Eloquent models

### HasSlug

The `HasSlug` trait adds automatic, unique slug generation to Eloquent models.

It generates slug from the `getSlugSource`, it ensures uniqueness at the database level, and keeps slug in sync when the source value changes.

Apply the trait to any Eloquent model:

```php
use SaeedHosan\Useful\Models\Concerns\HasSlug;

class Post extends Model
{
    use HasSlug;
}
```

> **Note** By default, the slug will be generated from the name attribute and stored in the slug column.

Slug Generation & Uniqueness

```php
Post::create(['name' => 'Fake Name'])->slug;     // fake-name
Post::create(['name' => 'Fake Name'])->slug;     // fake-name-1
Post::create(['name' => 'Fake Name'])->slug;     // fake-name-2
```

#### Customizable Slug keys and methods

```php
class Post extends Model
{
    use HasSlug;

    /**
     * Get the slug key name.
     */
    public function getSlugKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the source field for generating slugs.
     */
    public function getSlugSourceName(): string
    {
        return 'title';
    }

    /**
     * Generate a new unique slug for the model.
     */
    public function generateUniqueSlug(): string
    {
        // generate a unique slug
    }

    /**
     * Determine if the slug should be regenerated on update.
     */
    protected function shouldRegenerateSlug(): bool
    {
        return true;
    }
}
```

#### Finding a Model by Slug

```php
$post = Post::findBySlug('fake-name');
```

Returns the first matching model or null if no record exists.

---

A slug is generated if the slug column is empty.

```php
Post::create(['name' => 'My Test Name']);
// slug: my-test-name

// Get unique when slug by number for existing record
Post::create(['name' => 'My Test Name']);
// slug: my-test-name-1
```

---

The slug is regenerated when the source attribute changes.

```php
$post = Post::create(['name' => 'Original Name']);

$post->update(['name' => 'Updated Name']);
// slug: updated-name
```

### HasUuid

The `HasUuid` trait adds automatic UUID generation and lookup capabilities to Eloquent models.

It ensures every model receives a unique UUID on creation while allowing control over column naming and behavior.

Apply the trait to any Eloquent model:

```php
use SaeedHosan\Useful\Models\Concerns\HasUuid;

class Post extends Model
{
    use HasUuid;

    protected $fillable = ['name'];
}
```

When a model is created, a UUID is automatically generated and stored in the uuid column.

Find Model by UUID

```php
$post = Post::findByUuid($uuid);
```

Returns the matching model instance or null if no record is found.

---

Automatic UUID Generation

UUIDs are generated during the creating model event.

```php
$post = Post::create(['name' => 'Cyber']);

$post->uuid; // string (26 characters) by default
```

- UUIDs are unique per record
- Existing UUID values are never overridden

---

Accessing the UUID Value

```php
$post->getUuidKey();
```

Returns the UUID value for the model, or null if it has not been generated yet.

---

You may override the UUID column name by redefining getUuidKeyName():

```php
class Post extends Model
{
    use HasUuid;

    public function getUuidKeyName(): string
    {
        return 'public_id';
    }
}
```

---

If a UUID is manually provided, the trait will respect it:

```php
Post::create([
    'name' => 'Custom',
    'uuid' => 'custom-uuid',
]);
```

Database Considerations

For best results add a unique index on the UUID column

```php
$table->uuid('uuid')->unique();
```

### HasRouteBinding

This feature makes Eloquent’s `find()` method resolve using the **getRouteKeyName** instead of primary key.

It is useful when your models are identified by a slug, UUID, or any custom route key.

Apply the `HasRouteBinding` trait to your model:

```php
use SaeedHosan\Useful\Models\Concerns\HasRouteBinding;

class Post extends Model
{
    use HasRouteBinding;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
```

With the trait applied:

```php
Post::find('my-post-slug');
```

Is equivalent to:

```php
Post::where('slug', 'my-post-slug')->first();
```

Instead of querying by the primary key.

Example with UUIDs

```php
class Order extends Model
{
    use HasRouteBinding;

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}

Order::find('01HFYQ3P2YF4K8J9Q6Z8M2X7A1');
```

### HasStaticAccess

The `HasStaticAccess` trait provides a small set of static helpers for Eloquent models, allowing you to access common model metadata and queries without manually instantiating the model.

Why This Trait Exists?

This trait offers a clean, explicit way to do that while staying aligned with Laravel’s conventions.

Attach the trait to any Eloquent model:

```php
use SaeedHosan\Useful\Models\Concerns\HasStaticAccess;

class User extends Model
{
    use HasStaticAccess;
}
```

Available Static Access

```php
User::tableName();       // Returns the table name
User::routeKeyName();    // Returns the route key name
User::fields();          // Returns fillable attributes
User::findByKey($key);   // Find model by route key
User::findByRouteKey($key); // Alias of findByKey
```

- Static access without instantiate.
- Improved readability – Clear intent in routing, and helpers.
- Zero side effects – Uses fresh model instances internally.

## Eloquent

### BelongsToOne

The `BelongsToOne` relation provides a one-to-one relationship through a pivot table.
It behaves like `belongsToMany`, but returns a single related model insted of first.

Use the `HasBelongsToOne` trait and define the relation with the pivot table and keys:

```php
use Illuminate\Database\Eloquent\Model;
use SaeedHosan\Useful\Eloquent\Concerns\HasBelongsToOne;
use SaeedHosan\Useful\Eloquent\Relations\BelongsToOne;

class Blog extends Model
{
    use HasBelongsToOne;

    public function author(): BelongsToOne
    {
        return $this->belongsToOne(Author::class, 'author_blog', 'blog_id', 'author_id');
    }
}
```

Accessing the relation returns a single model (or `null`):

```php
$author = $blog->author;
```

Attach and update the relationship through the pivot table:

```php
$blog->author()->attach($authorId);
$blog->author()->sync([$authorId]);
```

**Eager Loading**

Eager load it like any other relation:

```php
$blogs = Blog::query()->with('author')->get();
```

## Commands

The package provides few commands that are frequntly used for every project

### Make action class

```bash
php artisan make:action CreateExampleAction
php artisan make:action CreateExampleAction -i # invokable
```

### EnvEditor

The `EnvEditor` support class provides a simple way to modify laravel environment variables.

It handles quoting, escaping, and ensures your environment configuration stays consistent.

```php
use SaeedHosan\Useful\Support\EnvEditor;

EnvEditor::addKey('APP_NAME', 'My Application');
// APP_NAME="My Application"

EnvEditor::editKey('APP_DEBUG', 'true');

EnvEditor::setKey('APP_URL', 'https://example.com');

EnvEditor::keyExists('APP_DEBUG'); // bool
```

The `put()` method works like `setKey()` and before check with `keyExists`:

```php
EnvEditor::put('DB_HOST', 'localhost'); // add new
EnvEditor::put('DB_HOST', '127.0.0.1'); // Updates existing
```
