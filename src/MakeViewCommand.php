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
    protected $signature = 'make:view {view} {--layout=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new blade template view in the resources/views folder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $viewDescriptor = $this->argument('view');
        $layoutOption = $this->option('layout');

        $path = $this->viewPath($viewDescriptor);

        $this->createDir($path);

        if (File::exists($path)) {
            $this->error("View file {$path} already exists!");
            return Command::FAILURE;
        }

        File::put($path, $layoutOption ? "@extends('layouts.$layoutOption')" : "");

        $this->components->info("View file {$path} created successfully.");

        return Command::SUCCESS;
    }

    /**
     * Get the view path from the string view descriptor
     *
     * @param string $viewDescriptor
     *
     * @return string
     */
    private function viewPath($viewDescriptor)
    {
        $viewDescriptor = str_replace('.', '/', $viewDescriptor) . '.blade.php';

        $path = 'resources/views/' . $viewDescriptor;

        return $path;
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
}
