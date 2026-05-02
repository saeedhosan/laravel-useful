<?php

declare(strict_types=1);

namespace SaeedHosan\Useful\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:response', description: 'Create a new http response class')]
class ResponseMakeClass extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Class';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->option('invokable')
            ? $this->resolveStubPath('/stubs/response.invokable.stub')
            : $this->resolveStubPath('/stubs/response.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @return string
     */
    protected function resolveStubPath(string $stub)
    {
        return file_exists($customPath = $this->laravel->basePath(mb_trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    #[Override]
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Responses';
    }

    /**
     * Get the console command arguments.
     *
     * @return array<int, array<int, mixed>>
     */
    #[Override]
    protected function getOptions()
    {
        return [
            ['invokable', 'i', InputOption::VALUE_NONE, 'Generate a single method, invokable action'],
            ['force', 'f', InputOption::VALUE_NONE, 'Create the action even if the class already exists'],
        ];
    }
}
