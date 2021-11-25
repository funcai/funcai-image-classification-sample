<?php

namespace App\Console\Commands;

use FuncAI\Applications\ImageClassification\ImageClassification;
use FuncAI\Applications\ImageClassification\ImageClassificationTrainingSample;
use Illuminate\Console\Command;

class ExportImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This exports the cats-dogs images in the format that FuncAI needs to train the model';

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

        // Fetch all cat/dog images from their folders
        $cats = $this->getImages(storage_path('app/PetImages/Cat'));
        $dogs = $this->getImages(storage_path('app/PetImages/Dog'));

        // We expect the classes for the image classification to be numeric, so we assign them a name so it's more readable.
        $CLASS_CATS = 0;
        $CLASS_DOGS = 1;

        // This is the main FuncAI class we'll be using
        $ai = new ImageClassification();

        // Add our training samples for cats and dogs
        foreach($cats as $cat) {
            // This is the most important step.
            // Here we tell FuncAI the absolute path to the training image and which class it has.
            // In your real world application you will probably fetch images from your database and add them here.
            $ai->addTrainingSample(new ImageClassificationTrainingSample(storage_path('app/PetImages/Cat') . '/' . $cat, $CLASS_CATS));
        }

        foreach($dogs as $dog) {
            $ai->addTrainingSample(new ImageClassificationTrainingSample(storage_path('app/PetImages/Dog') . '/' . $dog, $CLASS_DOGS));
        }

        $this->info('Starting the export. Please be patient :)');

        // Create the FuncAI export which contains all images
        $ai->exportTrainingData(storage_path('app/cats-dogs-export'));

        return Command::SUCCESS;
    }

    private function getImages($folder) {
        $files = collect(scandir($folder));
        return $files->filter(function($file) use ($folder) {
            // Only use image files
            $extension = strtolower(pathinfo($folder . $file, PATHINFO_EXTENSION));
            return in_array($extension, ['jpg', 'jpeg', 'png']);
        });
    }
}
