## FuncAI image classification example

This is an example project demonstrating how to use the FuncAI image classification.

### Running the project locally

 > This installation instruction assumes you have [docker](https://docs.docker.com/get-docker/) installed on your system. 

#### 1. Setup laravel

```
git clone
docker ... composer install
./vendor/bin/sail up
./vendor/bin/sail shell
./vendor/funcai/funcai-php/install.php
```

#### 2. Get the cats-dogs dataset
The cats-dogs dataset can be used to train a classifier which tells you if a given image contains a dog or a cat.

 - Download the [cats-dogs](https://www.microsoft.com/en-us/download/details.aspx?id=54765) dataset
 - Extract the zip file to the storage/app/ folder so that the final data structure looks like this:
   ```
   storage
     app
       PetImages
         Cat
         Dog
   ```

#### 3. Generate the FuncAI export
```
./vendor/bin/sail shell
php artisan images:export
```
Note that the export will take a while, because the images will be preprocessed for training.

In the meantime, feel free to have a look at `app/Console/Commands/ExportImage.php` to see how easy it is to create your own exports :)

#### 4. Train the model
It's time to train the model!

The following command will download a FuncAI docker container which then will train the image classifier based on the export from the previous step. 
```
docker run \
  -v $PWD/storage/app/cats-dogs-export/image-classification-export:/data \
  -e data=/data \
  -e performance=fast \
  funcai/funcai-train-image-classifier:latest
```
This command will take some time (depending on your cpu). Have a cup of tea in the meantime :)

#### 5. Try the model
Now we can reap the rewards of our previous work. We can finally predict wether a picture is a cat or a dog. To do that,
we enter the sail shell again and execute the `php artisan images:classify` command:
```
./vendor/bin/sail shell
php artisan images:classify
```
We now see, that an example dog picture was successfully classified as a dog!

#### 6. Make it your own
To classify your own images, change the export script to export your own images and then retrain the model with the command from step 4. 

> If you have any questions about how to train your own models, feel free to open a Github issue.
