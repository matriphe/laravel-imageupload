# Laravel Imageupload

Upload image using Laravel's build in function and resize it using Imagine library automatically.

## Compatibility

Works with Laravel 4 and Laravel 5. For Laravel 4.2, please refer to version 4.2.

## Installation

Open `composer.json` and require this line below.
```json
"matriphe/imageupload": "dev-master"
```
Or you can run this command from your project directory.
```bash
composer require "matriphe/format:dev-master"
```

### Laravel Installation

Open the `config/app.php` and add this line in `providers` section.
```php
'Matriphe\Imageupload\ImageuploadServiceProvider'
```
Still in `config/app.php`, add this line in `alias` section.
```php
'Imageupload' => 'Matriphe\Imageupload\ImageuploadFacade'
```

## Done

To check if it's installed, go to your web browser and hit `/matriphe/imageupload` from your URL. The uploaded file will be saved in `public/uploads` directory. You can change this by publishing and modifying configuration file.

## Publish Configuration

To control the configuration, you have to *publish* the configuration file.
```bash
php artisan vendor:publish
```
After running this command, there will be `config/imageupload.php` and `resources/views/vendor/imageupload/form.blade.php` files.

## Usage

Just use the `Imageupload::upload($file)` function and it will take care of cropping and renaming. Of course, you can modify on the fly by passing parameter `Imageupload::upload($filesource, $newfilename, $dir)`.
