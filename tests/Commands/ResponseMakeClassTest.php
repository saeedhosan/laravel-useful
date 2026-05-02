<?php

use Illuminate\Support\Facades\File;
use Tests\TestCase;

test('response make command creates a basic response class', function () {

    /** @var TestCase $this */
    $this->artisan('make:response', ['name' => 'TestResponse'])->assertExitCode(0);

    expect(File::exists(app_path('Http/Responses/TestResponse.php')))->toBeTrue();

    $content = File::get(app_path('Http/Responses/TestResponse.php'));
    expect($content)->toContain('class TestResponse implements Responsable');
    expect($content)->toContain('public function toResponse($request)');

    File::deleteDirectory(app_path('Http/Responses'));
});

test('response make command creates an invokable response', function () {

    /** @var TestCase $this */
    $this->artisan('make:response', [
        'name' => 'InvokableResponse',
        '--invokable' => true,
    ])->assertExitCode(0);

    expect(File::exists(app_path('Http/Responses/InvokableResponse.php')))->toBeTrue();

    $content = File::get(app_path('Http/Responses/InvokableResponse.php'));
    expect($content)->toContain('class InvokableResponse implements Responsable');
    expect($content)->toContain('public function __invoke($request)');
    expect($content)->toContain('public function toResponse($request)');

    File::deleteDirectory(app_path('Http/Responses'));
});
