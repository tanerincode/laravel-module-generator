<?php

namespace TanerInCode\ModuleGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AdapterGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     * module : Oluşturulacak classın hangi modulun içinde olacağını belirler.
     * className : Oluşturulacak classın adını belirler. interface adı otomatik olarak tanımlanır.
     */
    protected $signature = 'generate:adapter {module} {className} {--ask=true} {--first=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'TanerInCode Module > Adapter Generator';
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
        $className = $this->argument('className');
        $dontAsk = $this->option('ask');
        $first = $this->option('first');

        $modulePath = app_path('Modules/'.ucfirst($moduleName));
        if ( !File::isDirectory($modulePath) ){
            $this->error("Module Generator : This Module Not Exist !");
            return;
        }

        #paths
        $baseClassPath = app_path('Modules/'.ucfirst($moduleName));

        #fileNames
        $ClassMakePath = $baseClassPath."/".ucfirst($className)."Adapter.php";

        # check class
        if ( File::exists($ClassMakePath) )
        {
            $this->warn("Module Generator : This Adapter Already Exist !");
            $this->error("Module Generator: Generating Stopped!");
            return;
        }

        if ( $dontAsk == true )
            $question = $this->ask('Do you want to create an interface? (Y, N)');
        else
            $question =  'Y';

        if ( File::isDirectory($baseClassPath) ){
            if ( $question == "Y" ){
                if ( $first == false )
                    File::copy(__DIR__. config('mgenerator.src_url'). "setups/adapter/adapter.stub", $ClassMakePath); # get class Stub
                else
                    File::copy(__DIR__. config('mgenerator.src_url'). "setups/adapter/adapterExtendsClass.stub", $ClassMakePath); # get class Stub
            } else
                File::copy(__DIR__. config('mgenerator.src_url'). "/setups/adapter/adapterNoInterface.stub", $ClassMakePath); # get class Stub


            $classStub = File::get($ClassMakePath);
            $replaceForModule = str_replace('#Module#', $moduleName, $classStub);
            $replaceClassName = str_replace('#ClassName#', $className, $replaceForModule);

            # update class Stub
            File::put($ClassMakePath, $replaceClassName);
            $this->warn("Module Generator : Adapter Generated!");
        }else{
            $this->warn("Module Generator : Module Structure Not Ready for this command!");
            $this->MakeError = "error";
            return;
        }

        if ( strtoupper($question)  == 'Y' ){
            $interfaceMakePath = app_path('Modules/'.ucfirst($moduleName).'/Contracts/AdapterInterfaces')."/".ucfirst($className)."AdapterInterface.php";
            File::copy(__DIR__. config('mgenerator.src_url'). "setups/interfaces/adapter.stub", $interfaceMakePath);

            $classStub = File::get($interfaceMakePath);
            $replaceForModule = str_replace('#Module#', $moduleName, $classStub);
            $replaceInterfaceName = str_replace('#ClassName#', $className, $replaceForModule);

            # update interface Stub
            File::put($interfaceMakePath, $replaceInterfaceName);
            $this->warn("Module Generator : Adapter Interface Generated!");
        }

        if (!is_null($this->MakeError)){
            $this->error("Module Generator : Adapter Not Created!");
            return;
        }

        # return success message !
        $this->info("Module Generator : Adapter Generating Successfully!");
        return;
    }
}
