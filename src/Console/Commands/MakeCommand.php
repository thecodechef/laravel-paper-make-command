<?php

namespace LaravelPaper\MakeCommand\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

#[Signature('make:paper {model=Post} {path=} {--with-timestaps=false : Whether or not to use timestamps on the model.}
	{-m|--markdown : Create a Markdown Paper model with the builtin markdown driver.}
	{-j|--json : Create a Json Paper model with the builtin json driver.}
	{-y|--yaml : Create a YAML Paper model with a custom yaml driver.}
	{-t|--toml : Create a TOML Paper model with a custom toml driver.}
	{-d|--djot : Create a Djot Paper model with a custom djot driver.}
')]
#[Description('Create a Laravel Paper model based on a given Driver & ContentPath')]
class MakeCommand extends Command
{
    public function handle(): int
    {
        $modelInput = $this->argument('name');
        $modelName = Str::studly($modelInput);
        $driver = 'markdown';
        foreach (['json', 'yaml', 'toml', 'djot'] as $format) {
            if ($this->option($format)) {
                $driver = $format;
                break;
            }
        }
        $contentPath = $this->argument('path') ?? 'content/'.Str::plural(Str::kebab($modelName));

        $targetPath = app_path("Models/$modelName.php");

        if (File::exists($targetPath)) {
            $this->error("Model App\\Models\\$modelName already exists!");

            return self::FAILURE;
        }

        if ($this->option('with-timestamps')) {
            $stubPath = base_path('stubs/paper.timestamps.stub');
        } else {
            $stubPath = base_path('stubs/paper.basic.stub');
        }

        if (! File::exists($stubPath)) {
            $this->error("The stub file was not found at: $stubPath");

            return self::FAILURE;
        }

        $stubContent = File::get($stubPath);

        $replacements = [
            '[paper-model]' => $modelName,
            '[paper-driver]' => $driver,
            '[paper-path]' => $contentPath,
        ];

        $finalContent = str_replace(
            array_keys($replacements),
            array_values(replacements),
            $stubContent,
        );

        File::ensureDirectoryExists(dirname($targetPath));
        File::put($targetPath, $finalContent);

        $this->info("Laravel Paper Model [app/Models/$modelName.php] created succesfully.");

        return self::SUCCESS;
    }
}
