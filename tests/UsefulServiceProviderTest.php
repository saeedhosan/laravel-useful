<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use SaeedHosan\Useful\UsefulServiceProvider;
use Tests\TestCase;

test('service provider has register and boot methods', function () {
    /** @var TestCase $this */
    $provider = new UsefulServiceProvider($this->app);

    expect(method_exists($provider, 'register'))->toBeTrue();
    expect(method_exists($provider, 'boot'))->toBeTrue();
});

test('service provider registers action make command', function () {
    /** @var TestCase $this */
    $provider = new UsefulServiceProvider($this->app);
    $provider->boot();

    $commands = Artisan::all();
    expect(isset($commands['make:action']))->toBeTrue();
});

test('service provider publishes stubs', function () {
    /** @var TestCase $this */
    new UsefulServiceProvider($this->app);

    $this->artisan('vendor:publish', ['--tag' => 'useful-stubs'])->assertExitCode(0);

    expect(File::exists(base_path('stubs/action.stub')))->toBeTrue();
    expect(File::exists(base_path('stubs/action.invokable.stub')))->toBeTrue();
});
