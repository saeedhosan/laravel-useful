## Support

A list of support classes are includes in the package

### CreateInstance

The `CreateInstance` provides convenient ways to create class instances using static cache.

Attach the trait to any class:

```php
use SaeedHosan\Useful\Support\Traits\CreateInstance;

class ReportGenerator
{
    use CreateInstance;

    public function __construct(private string $name) {}

    public function getName(){
        return $this->name;
    }
}
```

Creating Instances via the Container

Use `make()` to resolve the class through Laravel’s service container.

```php
$report = ReportGenerator::make('Laravel is')->getName(); // Laravel is
```

### PreventInstance

The `PreventInstance` trait ensures a class cannot be instantiated.

Apply the trait to a class meant for static usage only:

```php
use SaeedHosan\Useful\Support\Traits\PreventInstance;

class StringHelpers
{
    use PreventInstance;

    public static function upper(string $value): string
    {
        return strtoupper($value);
    }
}
```

Attempting to instantiate the class will throw a LogicException:

```php
new StringHelpers();
// LogicException: StringHelpers cannot be instantiated.
```

### Path

The `Path` class provides simple, cross-platform utilities for working with file system paths.
It focuses on **normalization**, **joining**, and **safe path inspection**, without side effects.

Join multiple path segments into a clean, normalized path:

```php
use SaeedHosan\Useful\Support\Path;

Path::join('storage', 'app', 'files');
// storage/app/files
```

Handles duplicate slashes and mixed separators automatically.

---

Normalize a path by: Converting `\` to `/` Removing `./` and removing duplicate slashes

```php
Path::normalize('storage\\app//./files');
// storage/app/files
```

---

Get the directory of the file where Path::current() is called:

```php
Path::current('config', 'files');
// /current/file/dir/config/files
```

Useful for resolving paths relative to the calling file.

---

Resolving Real Paths - Resolve a path to its absolute form (if it exists):

```php
Path::real('./storage/app');
// /full/path/to/storage/app
```

Returns `null` if the path does not exist.

---

Replacing Path Segments - Replace the first occurrence:

```php
Path::replaceFirst('storage', 'public', 'storage/app/file.txt');
// public/app/file.txt
```

Replace all occurrences:

```php
Path::replace('/', '-', 'storage/app/file.txt');
// storage-app-file.txt
```

---

Path Information - Get common path parts:

```php
Path::dirname('/var/www/index.php');
// /var/www

Path::basename('/var/www/index.php');
// index.php

Path::filename('/var/www/index.php');
// index

Path::extension('/var/www/index.php');
// php
```

---

Absolute Path Detection - Check if a path is absolute (Linux or Windows):

```php
Path::isAbsolute('/var/www');     // true
Path::isAbsolute('C:\\Windows'); // true
Path::isAbsolute('storage/app'); // false
```

### Json

The `Json` class provides a simple, safe way to **check** and **decode** JSON values without throwing errors.

It is designed for defensive code where input may be invalid, empty, or unknown.

**Checking if a Value Is JSON**

Use `Json::is()` to determine whether a value is a **valid JSON string**.

```php
use SaeedHosan\Useful\Support\Json;

Json::is('{"name":"Saeed"}'); // true
Json::is('[1,2,3]');          // true
Json::is('"string"');         // true
Json::is('null');             // true

Json::is('{invalid-json}');   // false
Json::is('');                 // false
Json::is(123);                // false
Json::is(null);               // false
```

Only valid JSON strings return true.

---

**Decoding JSON Safely**

Use Json::decode() to decode JSON into an array without exceptions.

```php
Json::decode('{"name":"Saeed"}');
// ['name' => 'Saeed']

Json::decode('[1,2,3]');
// [1, 2, 3]
```

**Default Fallback Value**

You may provide a default value when decoding fails:

```php
$default = ['default' => true];

Json::decode(null, $default);            // ['default' => true]
Json::decode('{invalid-json}', $default); // ['default' => true]
Json::decode('"string"', $default);       // ['default' => true]
Json::decode('123', $default);             // ['default' => true]
```

**Summary**

- `Json::is()` checks if a value is valid JSON
- `Json::decode()` safely decodes JSON into an array
- No exceptions, no warnings
- Ideal for handling user input, config values, or external data
