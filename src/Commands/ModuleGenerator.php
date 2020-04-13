<?php

namespace TanerInCode\ModuleGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ModuleGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:module {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'TanerInCode Module > Directory Generator';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(5);
        $bar->setFormat('Progress: %current%/%max% -> <info>%message%</info>');
        $bar->start();

        $bar->setMessage('Start Generating!');

        $module = $this->argument('module');
        if ( File::isDirectory(app_path('Modules/'.$module)) )
        {
            $bar->finish();
            $bar->clear();
            $this->warn('This module already exist!');
            return;
        }

        File::copyDirectory(__DIR__ . config('mgenerator.src_url') .'setups/directories/ModuleName',app_path('Modules/'.$module));
        $bar->setMessage('Created Directories!');
        $bar->advance();
        sleep(1);

        # create main class
        Artisan::call("generate:controller", [
            'module' => $module,
            'className' => $module
        ]);
        $bar->setMessage('Created Main Class!');
        $bar->advance();
        sleep(1);

        # create service
        Artisan::call("generate:service", [
            'module' => $module,
            'fileName' => '',
            '--first' => true
        ]);
        $bar->setMessage('Created Service!');
        $bar->advance();
        sleep(1);

        # create repository
        Artisan::call("generate:repository", [
            'module' => $module,
            'fileName' => '',
            '--first' => true
        ]);
        $bar->setMessage('Created Repository!');
        $bar->advance();
        sleep(1);

        # create provider
        Artisan::call("generate:provider", [
            'module' => $module,
            'providerName' => $module
        ]);
        $bar->setMessage('Created Provider!');
        $bar->advance();
        sleep(1);

        $bar->setMessage("");
        $bar->finish();
        $bar->clear();

        $this->info("Module Generated !");
        return;
    }

}
