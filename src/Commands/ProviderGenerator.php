<?php

namespace TanerInCode\ModuleGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ProviderGenerator extends Command
{
    private $makePath = '';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:provider {module} {providerName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'TanerInCode Module > Provider  Generator';
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
        $providerName = $this->argument('providerName');

        // check module exist ?
        $modulePath = app_path('Modules/'.ucfirst($moduleName));
        if ( !File::isDirectory($modulePath) )
        {
            $this->warn("Module Generator : This Module Not Exist !");
            return;
        }

        $this->makePath =  app_path('Modules/'.ucfirst($moduleName).'/');
        $providerMakePath = $this->makePath."/".ucfirst($providerName)."ServiceProvider.php";

        # check class
        if ( File::exists($providerMakePath) )
        {
            $this->error("Module Generator : This Provider Already Exist !");
            return;
        }

        if ( File::isDirectory($this->makePath) ){
                $this->copySetupAndReplace($providerMakePath, $moduleName, $providerName);
        } else {
            $this->warn("Module Generator : Module Structure Not Ready for this command!");
            $this->MakeError = "error";
        }

        #paths
        $baseInterfacePath = app_path('Modules/'.ucfirst($moduleName).'/');

        #fileNames
        $interfaceMakePath = $baseInterfacePath."/".ucfirst($providerName)."ServiceProvider.php";

        # check class
        if ( File::exists($interfaceMakePath) )
        {
            $this->error("Module Generator : This Provider Already Exist !");
            return;
        }
        if (!is_null($this->MakeError))
            $this->error("Module Generator : Provider Not Created!");


        $this->info("Module Generator : Provider Created !");
        return;
    }

    private function copySetupAndReplace($interfaceMakePath, $moduleName, $interfaceName)
    {
        File::copy(__DIR__ . config('mgenerator.src_url') . "setups/providers/serviceprovider.stub", $interfaceMakePath);

        $classStub = File::get($interfaceMakePath);
        $replaceForNameSpace = str_replace('#name_space#', config('mgenerator.name_space'), $classStub);
        $replaceForModule = str_replace('#Module#', $moduleName, $replaceForNameSpace);
        $replaceInterfaceName = str_replace('#ClassName#', $interfaceName, $replaceForModule);

        # update interface Stub
        File::put($interfaceMakePath, $replaceInterfaceName);
    }
}
