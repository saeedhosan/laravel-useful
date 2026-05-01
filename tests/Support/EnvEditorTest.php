<?php

use Illuminate\Support\Facades\File;
use SaeedHosan\Useful\Support\EnvEditor;

afterEach(function () {
    if (File::exists(EnvEditor::envPath())) {
        File::delete(EnvEditor::envPath());
    }
});

test('adds a new key to the env file', function () {

    EnvEditor::add('APP_NAME', 'My App');

    $content = File::get(EnvEditor::envPath());

    expect($content)->toContain('APP_NAME="My App"');
});

test('properly escapes quotes in values', function () {

    EnvEditor::add('APP_KEY', 'value with "quotes"');

    expect(File::get(EnvEditor::envPath()))
        ->toContain('APP_KEY="value with \"quotes\""');
});

test('edits an existing key', function () {

    File::put(EnvEditor::envPath(), "APP_NAME=\"Old Name\"\n");

    EnvEditor::update('APP_NAME', 'New Name');

    $content = File::get(EnvEditor::envPath());
    expect($content)->not->toContain('APP_NAME="Old Name"');

    expect($content)->toBe("APP_NAME=\"New Name\"\n");
});

test('only affects targeted key', function () {

    File::put(
        EnvEditor::envPath(),
        "APP_NAME=Old\nDB_HOST=localhost\nDB_PORT=3306"
    );

    EnvEditor::update('DB_HOST', 'new_host');

    $content = File::get(EnvEditor::envPath());
    expect($content)->toContain('APP_NAME=Old');
    expect($content)->toContain('DB_HOST="new_host"');
    expect($content)->toContain('DB_PORT=3306');
});

test('properly escapes quotes in values for update', function () {
    File::put(EnvEditor::envPath(), 'APP_KEY=test');

    EnvEditor::update('APP_KEY', 'value with "quotes"');

    expect(File::get(EnvEditor::envPath()))
        ->toContain('APP_KEY="value with \"quotes\""');
});

test('adds or edits correctly', function () {
    EnvEditor::put('DB_NAME', 'local db');
    expect(File::get(EnvEditor::envPath()))->toContain('DB_NAME="local db"');

    EnvEditor::put('DB_NAME', 'changed');
    expect(File::get(EnvEditor::envPath()))->toContain('DB_NAME="changed"');
});

test('adds new key when it does not exist', function () {
    File::put(EnvEditor::envPath(), 'EXISTING=value');

    EnvEditor::put('NEW_KEY', 'new_value');

    expect(File::get(EnvEditor::envPath()))->toContain('NEW_KEY="new_value"');
});

test('edits existing evn key', function () {
    File::put(EnvEditor::envPath(), 'APP_NAME=Old');

    EnvEditor::put('APP_NAME', 'New');

    expect(File::get(EnvEditor::envPath()))->toContain('APP_NAME="New"');
});

test('preserves other keys when editing', function () {
    File::put(EnvEditor::envPath(), "APP_NAME=Test\nDB_HOST=localhost");

    EnvEditor::put('APP_NAME', 'Updated');

    $content = File::get(EnvEditor::envPath());
    expect($content)->toContain('APP_NAME="Updated"');
    expect($content)->toContain('DB_HOST=localhost');
});

test('handles value with quotes for put', function () {
    EnvEditor::put('QUOTED_KEY', 'value with "quotes"');

    expect(File::get(EnvEditor::envPath()))
        ->toContain('QUOTED_KEY="value with \"quotes\""');
});

test('reloadConfig  executes without error', function () {
    EnvEditor::reloadConfig();
    expect(true)->toBeTrue();
});
