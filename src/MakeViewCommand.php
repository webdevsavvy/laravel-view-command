<?php

namespace Webdevsavvy\LaravelViewCommand;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeViewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:view
                            { view : The name and final path of the view following the . separator convention for file paths }
                            { --layout= : The name of the layout that this view extends }
                            { --resource : Generate all the views needed for a resource controller }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new blade template views in the resources/views directory';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $viewDescriptor = $this->argument('view');
        $layoutOption = $this->option('layout');
        $resourceOption = $this->option('resource');

        if (!$viewDescriptor) {
            $viewDescriptor = $this->ask('What name should the view or directory have?');

            $layoutOption = $this->confirm('Should the views extend a layout?') ? $this->ask('What is the name of the layout?') : null;

            $resourceOption = $this->confirm('Create all the views for a resource?');
        }

        $paths = $resourceOption ? $this->generateResourceViewsPaths($viewDescriptor) : [$this->viewPath($viewDescriptor)];

        foreach ($paths as $path) {

            $this->createDir($path);

            if (File::exists($path)) {
                $this->error("View file {$path} already exists!");
                return Command::FAILURE;
            }

            File::put($path, $layoutOption ? "@extends('layouts.$layoutOption')" : "");

            $this->components->info("View file {$path} created successfully.");
        }

        return Command::SUCCESS;
    }

    /**
     * Get the view path from the string view descriptor
     *
     * @param string $viewDescriptor
     *
     * @return string
     */
    private function viewPath(string $viewDescriptor)
    {
        return resource_path('views/') . str_replace('.', '/', $viewDescriptor) . '.blade.php';
    }

    /**
     * Create the view directory if not exists
     *
     * @param string $path
     */
    private function createDir($path)
    {
        $dir = dirname($path);

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    private function generateResourceViewsPaths(string $viewDescriptor)
    {
        $defaultViewPaths = ['create', 'edit', 'show', 'index'];

        return array_map(function ($defaultPath) use ($viewDescriptor) {

            return $this->viewPath($viewDescriptor . '.' . $defaultPath);
        }, $defaultViewPaths);
    }
}