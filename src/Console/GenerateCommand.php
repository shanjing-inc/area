<?php

namespace TemporaryCrash\Area\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;


class GenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'area:generate
            {--file= : file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build table and generate data';

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $file = __DIR__.'/../Resources/2016_7_31.log';


        $this->buildTable();
        $count = $this->generateDataFromFile($file);
        $this->info('There are ' . $count . ' pieces of data');
        return;
    }


    protected function buildTable()
    {
        \Schema::dropIfExists('areas');


        \Schema::create('areas', function (Blueprint $table) {
            $table->unsignedMediumInteger('postcode')->comment('行政区划代码');
            $table->unsignedMediumInteger('parent')->comment('父代码');
            $table->string('entity')->comment('单位名称');
            $table->unique('postcode');
            $table->index('parent');
        });

    }

    protected function generateDataFromFile($file)
    {
        $source = explode("\n",file_get_contents($file));

        $search = [" ","　","\t","\n","\r"];
        $replace = ["","","","",""];
        collect($source)->map(function ($value) use ($search,$replace) {

            $tmp = str_replace($search,$replace,$value);

            $postcode = substr($tmp,0,6);
            $entity = substr($tmp,6);
            \DB::table('areas')->insert([
                'postcode' => $postcode,
                'entity' => $entity,
                'parent' => $this->checkParent($postcode)
            ]);
        });

        return count($source);

    }

    protected function checkParent($postcode){
        if(substr($postcode,-2,2) !== '00'){
            return substr($postcode,0,4).'00';
        }

        if(substr($postcode,-4,4) !== '0000'){
            return substr($postcode,0,2).'0000';
        }

        return '100000';
    }
}