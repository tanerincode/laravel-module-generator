<?php

namespace TanerInCode\ModuleGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ServiceGenerator extends Command
{
    private $makePath = '';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:service {module} {fileName} {--first=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'TanerInCode Module > Service generator.';
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
        $fileName = $this->argument('fileName');
        $first = $this->option('first');

        // check module exist ?
        $modulePath = app_path('Modules/'.ucfirst($moduleName));

        if ( !File::isDirectory($modulePath) )
        {
            $this->warn("Module Generator : This Module Not Exist !");
            return;
        }

        if ( $first == false )
            $question = $this->ask('Do you want to create an interface? (Y, N)');
        else
            $question =  'Y';

        $this->makePath =  $modulePath.'/Services';
        $moduleMakePath = $this->makePath."/".ucfirst($fileName)."Service.php";

        # check class
        if ( File::exists($moduleMakePath) )
        {
            $this->error("Module Generator : This Service Already Exist !");
            return;
        }

        if ( File::isDirectory($this->makePath) ){
                $this->copySetupAndReplace($moduleMakePath, $moduleName, $fileName, $question);
        } else {
            $this->warn("Module Generator : Module Structure Not Ready for this command!");
            $this->MakeError = "error";
        }

        if (!is_null($this->MakeError)){
            $this->error("Module Generator : Service Not Created!");
            return;
        }

        $this->info("Module Generator : Service Created !");
        return;
    }

    private function copySetupAndReplace($interfaceMakePath, $moduleName, $interfaceName, $question)
    {
        if ( $question == "Y" ){
            File::copy(__DIR__ . config('mgenerator.src_url') . "setups/services/serviceImplementByInterface.stub", $interfaceMakePath);
            Artisan::call("generate:interface",[
                'module' => $moduleName,
                'interfaceName' => $interfaceName,
                '--type' => 'service'
            ]);
        }
        else
            File::copy(__DIR__ . config('mgenerator.src_url') . "setups/services/service.stub", $interfaceMakePath);

        $classStub = File::get($interfaceMakePath);
        $replaceForNameSpace = str_replace('#name_space#', config('mgenerator.name_space'), $classStub);
        $replaceForModule = str_replace('#Module#', $moduleName, $replaceForNameSpace);
        $replaceInterfaceName = str_replace('#ClassName#', $interfaceName, $replaceForModule);

        # update interface Stub
        File::put($interfaceMakePath, $replaceInterfaceName);
    }
}
