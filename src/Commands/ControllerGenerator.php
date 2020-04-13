<?php

namespace TanerInCode\ModuleGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ControllerGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     * module : Oluşturulacak classın hangi modulun içinde olacağını belirler.
     * className : Oluşturulacak classın adını belirler. interface adı otomatik olarak tanımlanır.
     */
    protected $signature = 'generate:controller {module} {className}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'TanerInCode Module > Class Generator';
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

        $modulePath = app_path('Modules/'.ucfirst($moduleName));
        if ( !File::isDirectory($modulePath) ){
            $this->error("Module Generator : This Module Not Exist !");
            return;
        }

        #paths
        $baseClassPath = app_path('Modules/'.ucfirst($moduleName).'/Controllers');

        #fileNames
        $ClassMakePath = $baseClassPath."/".ucfirst($className)."Controller.php";


        # check class
        if ( File::exists($ClassMakePath) )
        {
            $this->warn("Module Generator : This Class Already Exist !");
            $this->error("Module Generator: Generating Stopped!");
            return;
        }

        if ( File::isDirectory($baseClassPath) ){
            File::copy(__DIR__ . config('mgenerator.src_url') ."setups/classes/controllers.stub", $ClassMakePath); # get class Stub

            $classStub = File::get($ClassMakePath);
            $replaceForNameSpace = str_replace('#name_space#', config('mgenerator.name_space'), $classStub);
            $replaceForModule = str_replace('#Module#', $moduleName, $replaceForNameSpace);
            $replaceClassName = str_replace('#ClassName#', $className, $replaceForModule);

            # update class Stub
            File::put($ClassMakePath, $replaceClassName);
            $this->warn("Module Generator : Controller Generated!");
        }else{
            $this->warn("Module Generator : Module Structure Not Ready for this command!");
            $this->MakeError = "error";
            return;
        }

        if (!is_null($this->MakeError)){
            $this->error("Module Generator : Class Not Created!");
            return;
        }

        # return success message !
        $this->info("Module Generator : Class Generating Successfully!");
        return;
    }
}
