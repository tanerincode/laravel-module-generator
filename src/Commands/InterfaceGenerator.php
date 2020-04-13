<?php

namespace TanerInCode\ModuleGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InterfaceGenerator extends Command
{
    private $makePath = '';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:interface {module} {interfaceName} {--type=class}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'TanerInCode Module > Interface Generator';
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
        $interfaceName = $this->argument('interfaceName');
        $option = $this->option("type");

        // check module exist ?
        $modulePath = app_path('Modules/'.ucfirst($moduleName));
        if ( !File::isDirectory($modulePath) )
        {
            $this->warn("Module Generator : This Module Not Exist !");
            return;
        }

        $this->catchMakePath($option,$moduleName);

        if ( $option == 'class' )
            $interfaceMakePath = $this->makePath."/".ucfirst($interfaceName)."Interface.php";
        else
            $interfaceMakePath = $this->makePath."/".ucfirst($interfaceName).ucfirst($option)."Interface.php";

        # check class
        if ( File::exists($interfaceMakePath) )
        {
            $this->error("Module Generator : This Interface Already Exist !");
            return;
        }

        if ( File::isDirectory($this->makePath) ){
                $this->copySetupAndReplace($option, $interfaceMakePath, $moduleName, $interfaceName);
        } else {
            $this->warn("Module Generator : Module Structure Not Ready for this command!");
            $this->MakeError = "error";
        }

        #paths
        $baseInterfacePath = app_path('Modules/'.ucfirst($moduleName).'/');

        #fileNames
        $interfaceMakePath = $baseInterfacePath."/".ucfirst($interfaceName)."Interface.php";

        # check class
        if ( File::exists($interfaceMakePath) )
        {
            $this->error("Module Generator : This Interface Already Exist !");
            return;
        }
        if (!is_null($this->MakeError))
            $this->error("Module Generator : Interface Not Created!");


        $this->info("Module Generator : Interface Created !");
        return;
    }

    private function copySetupAndReplace($option, $interfaceMakePath, $moduleName, $interfaceName)
    {

        File::copy(__DIR__. config('mgenerator.src_url') ."setups/interfaces/".$option.".stub", $interfaceMakePath);

        $classStub = File::get($interfaceMakePath);
        $replaceForNameSpace = str_replace('#name_space#', config('mgenerator.name_space'), $classStub);
        $replaceForModule = str_replace('#Module#', $moduleName, $replaceForNameSpace);
        $replaceInterfaceName = str_replace('#ClassName#', $interfaceName, $replaceForModule);

        # update interface Stub
        File::put($interfaceMakePath, $replaceInterfaceName);
    }

    private function catchMakePath($option, $moduleName)
    {
        switch ($option)
        {
            case "repository":
                $this->makePath =  app_path('Modules/'.ucfirst($moduleName).'/Repositories');
                break;
            case "service":
                $this->makePath =  app_path('Modules/'.ucfirst($moduleName).'/Services');
                break;
            default:
                $this->makePath = app_path('Modules/'.ucfirst($moduleName).'/App/Interfaces');
        }
    }
}
