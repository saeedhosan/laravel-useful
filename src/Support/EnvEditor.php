<?php

declare(strict_types=1);

namespace SaeedHosan\Useful\Support;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class EnvEditor
{
    /**
     * Check if a key exists in the .env file.
     */
    public static function has(string $key): bool
    {
        if (! File::exists(static::envPath())) {
            return false;
        }

        $content = File::get(static::envPath());

        return preg_match("/^{$key}=.*$/m", $content) === 1;
    }

    public static function update(string $key, string $value): bool
    {
        if (! File::exists(static::envPath())) {
            return false;
        }

        $content = File::get(static::envPath());

        $quotedValue = '"'.addcslashes($value, '"').'"';

        $pattern = "/^{$key}=.*$/m";

        if (preg_match($pattern, $content)) {

            $content = (string) preg_replace($pattern, "{$key}={$quotedValue}", $content);

            File::put(static::envPath(), $content);

            return true;
        }

        return false;
    }

    /**
     * Add a new key=value pair to the .env file.
     */
    public static function add(string $key, string $value): bool
    {
        if (! File::exists(static::envPath())) {
            File::put(static::envPath(), '');
        }

        $quotedValue = '"'.addcslashes($value, '"').'"';

        $line = PHP_EOL."{$key}={$quotedValue}".PHP_EOL;

        /** @var int|false $result */
        $result = File::append(static::envPath(), $line);

        return (bool) $result;
    }

    /**
     * Optionally reload Laravel's configuration in memory.
     */
    public static function reloadConfig(): void
    {

        if (! function_exists('app')) {
            return;
        }

        Artisan::call('config:clear');
        Artisan::call('config:cache');
        Artisan::call('queue:restart');
        Config::clearResolvedInstances();
    }

    /**
     * Put the value into environment variable for the given env key.
     */
    public static function put(string $key, string $value): bool
    {
        return static::has($key)
            ? static::update($key, $value)
            : static::add($key, $value);
    }

    /**
     * Get the current environment file path.
     */
    public static function envPath(): string
    {
        return app()->environmentFilePath();
    }
}
