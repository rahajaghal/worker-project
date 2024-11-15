<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;


class CreateServiceClass extends FileFactoryCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {classname}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'this command for create service class pattern';

    /**
     * Execute the console command.
     */
//    protected $file;
//    public function __construct(Filesystem $file)
//    {
//        parent::__construct();
//        $this->file=$file;
//    }
//    public function singleClassName($name)
//    {
//        return ucwords(Pluralizer::singular($name));
//    }
//    public function makeDir($path)
//    {
//        $this->file->makeDirectory($path,0777,true,true);
//        return $path;
//    }
//    public function stubPath()
//    {
//        return __DIR__."/../../../stubs/servicepattern.stub";
//    }
//    public function stubVariables()
//    {
//        return [
//            'NAME'=>$this->singleClassName($this->argument('classname'))
//        ];
//    }
//    public function stubContent($stubPath,$stubVariables)
//    {
//        $content= file_get_contents($stubPath);
//        foreach ($stubVariables as $search =>$name){
//            $content=str_replace('$' .$search,$name,$content);
//        }
//        return $content;
//    }
//
//    public function getPath()
//    {
//        return base_path("App\\Services\\").$this->singleClassName($this->argument('classname')). "Service.php";
//    }
//    public function handle()
//    {
//        $path=$this->getPath();
//        $this->makeDir(dirname($path));
//        if ($this->file->exists($path)){
//            $this->info('this file already exists');
//        }
//        $stubPath =$this->stubPath();
//        $content =$this->stubContent($stubPath,$this->stubVariables());
//        $this->file->put($path,$content);
//        $this->info('this file has been created');
//    }
    function setFilePath():string
    {
        return  "App\\Services\\";
    }
     function setStubName():string
    {
        return  "servicepattern";
    }
    function setSuffix():string
    {
        return  "Service";
    }
}
