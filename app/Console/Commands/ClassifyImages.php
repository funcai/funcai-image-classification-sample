<?php

namespace App\Console\Commands;

use FuncAI\Applications\ImageClassification\ImageClassification;
use Illuminate\Console\Command;

class ClassifyImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:classify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This uses the previously trained model to classify images of cats and dogs';

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
     * @return int
     */
    public function handle()
    {
        // FuncAI setup
        \FuncAI\Config::setModelBasePath(storage_path('models'));
        \FuncAI\Config::setLibPath('./tensorflow/');

        // We expect the classes for the image classification to be numeric, so we assign them a name so it's more readable.
        $CLASS_CATS = 0;
        $CLASS_DOGS = 1;

        // This is the main FuncAI class we'll be using
        $ai = new ImageClassification();
        $ai->setTask('cats_dogs');
        $ai->setPerformance(ImageClassification::PERFORMANCE_BALANCED);

        $dog = storage_path('app/cats-dogs-export/image-classification-export/' . $CLASS_DOGS . '/1.jpg');

        $dogIsADog = $ai->predict($dog)[$CLASS_DOGS] >= 0.5;
        if($dogIsADog) {
            $this->info('Detected a dog in the dog image!');
        } else {
            $this->error('Detected no dog in the dog image!');
        }

        return Command::SUCCESS;
    }
}
