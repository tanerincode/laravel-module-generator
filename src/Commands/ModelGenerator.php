<?php

namespace TanerInCode\ModuleGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ModelGenerator extends Command
{
    private $makePath = '';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:model {module} {modelName} {--first=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'TanerInCode Module > Model generator.';
    /**
     * @var string
     */
    private $MakeError;

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
        # get arguments
        $moduleName = $this->argument('module');
        $modelName = $this->argument('modelName');
        $first = $this->option('first');

        // check module exist ?
        $modulePath = app_path('Modules/'.ucfirst($moduleName));

        if ( !File::isDirectory($modulePath) )
        {
            $this->warn("Module Generator : This Module Not Exist !");
            return;
        }

        $this->makePath =  $modulePath.'/Models';
        $moduleMakePath = $this->makePath."/".ucfirst($modelName)."Model.php";

        # check class
        if ( File::exists($moduleMakePath) )
        {
            $this->error("Module Generator : This Model Already Exist !");
            return;
        }

        if ( File::isDirectory($this->makePath) ){
                $this->copySetupAndReplace($moduleMakePath, $moduleName, $modelName, $first);
        } else {
            $this->warn("Module Generator : Module Structure Not Ready for this command!");
            $this->MakeError = "error";
        }

        if (!is_null($this->MakeError))
            $this->error("Module Generator : Model Not Created!");


        $this->info("Module Generator : Model Created !");
        return;
    }

    private function copySetupAndReplace($interfaceMakePath, $moduleName, $interfaceName, $first)
    {
        if ( $first  == false )
            File::copy(__DIR__ . config('mgenerator.src_url') . "setups/Model/model.stub", $interfaceMakePath);
        else
            File::copy(__DIR__ . config('mgenerator.src_url') . "setups/Model/modelNoExtends.stub", $interfaceMakePath);

        $classStub = File::get($interfaceMakePath);
        $replaceForModule = str_replace('#Module#', $moduleName, $classStub);
        $replaceInterfaceName = str_replace('#ClassName#', $interfaceName, $replaceForModule);

        # update interface Stub
        File::put($interfaceMakePath, $replaceInterfaceName);
    }
}
