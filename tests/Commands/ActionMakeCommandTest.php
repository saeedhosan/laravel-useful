<?php

use Illuminate\Support\Facades\File;
use Tests\TestCase;

test('action make command creates a basic action class', function () {

    /** @var TestCase $this */
    $this->artisan('make:action', ['name' => 'TestAction'])->assertExitCode(0);

    expect(File::exists(app_path('Actions/TestAction.php')))->toBeTrue();

    $content = File::get(app_path('Actions/TestAction.php'));
    expect($content)->toContain('final readonly class TestAction');
    expect($content)->toContain('public function handle(): void');
    expect($content)->toContain('DB::transaction');

    File::deleteDirectory(app_path('Actions'));
});

test('action make command creates an invokable action', function () {

    /** @var TestCase $this */
    $this->artisan('make:action', [
        'name' => 'InvokableAction',
        '--invokable' => true,
    ])->assertExitCode(0);

    expect(File::exists(app_path('Actions/InvokableAction.php')))->toBeTrue();

    $content = File::get(app_path('Actions/InvokableAction.php'));
    expect($content)->toContain('final readonly class InvokableAction');
    expect($content)->toContain('public function __invoke(): void');
    expect($content)->toContain('DB::transaction');

    File::deleteDirectory(app_path('Actions'));
});
