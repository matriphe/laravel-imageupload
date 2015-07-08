# Laravel Imageupload

Upload image using Laravel's build in function and resize it using [Imagine library](https://imagine.readthedocs.org/en/latest/) automatically.

## Compatibility

* Laravel 5.1 (Latest)
* [Laravel 5.0](https://github.com/matriphe/laravel-imageupload/blob/laravel50/README.md)
* [Laravel 4.2](https://github.com/matriphe/laravel-imageupload/blob/laravel42/README.md)

## Installation

Open `composer.json` and require this line below.
```json
"matriphe/imageupload": "5.1.*"
```
Or you can run this command from your project directory.
```bash
composer require "matriphe/imageupload:5.1.*"
```

### Laravel Installation

Open the `config/app.php` and add this line in `providers` section.
```php
Matriphe\Imageupload\ImageuploadServiceProvider::class,
```
Still in `config/app.php`, add this line in `aliases` section.
```php
'Imageupload' => Matriphe\Imageupload\ImageuploadFacade::class,
```

## Publish Configuration

To control the configuration, you have to *publish* the configuration file.
```bash
php artisan vendor:publish
```
After running this command, there will be `config/imageupload.php` and `resources/views/vendor/imageupload/form.blade.php` files.

## Done

After publishing the configuration and views, you can check by hitting `http://yoururl/matriphe/imageupload` to see if the installation is perfect.

The uploaded file will be saved in `public/uploads` directory. Of course, you can change this by publishing and modifying configuration file.

## Usage

Just use the `Imageupload::upload($file)` function and it will take care of cropping and renaming. Of course, you can modify on the fly by passing parameter `Imageupload::upload($filesource, $newfilename, $dir)`.

The return of the function is **array**.

### Example
```php
if (Request::hasFile('file')) {
    $result = Imageupload::upload(Request::file('file'));
}
```
### Output
```array
Array
(
    [path] => /Users/matriphe/www/laravel/5.1/public/uploads/images
    [dir] => uploads/images
    [original_filename] => Xiaomi Media Invitation Final.png
    [original_filepath] => /Users/matriphe/www/laravel/5.1/public/uploads/images/424370e1611a171b99b5c6ec20aaeedf.png
    [original_extension] => png
    [original_filesize] => 129840
    [original_mime] => image/png
    [filename] => 424370e1611a171b99b5c6ec20aaeedf.png
    [original_filedir] => uploads/images/424370e1611a171b99b5c6ec20aaeedf.png
    [basename] => 424370e1611a171b99b5c6ec20aaeedf
    [original_width] => 1281
    [original_height] => 816
    [dimensions] => Array
        (
            [square50] => Array
                (
                    [path] => /Users/matriphe/www/laravel/5.1/public/uploads/images
                    [dir] => uploads/images
                    [filename] => 424370e1611a171b99b5c6ec20aaeedf_square50.png
                    [filepath] => /Users/matriphe/www/laravel/5.1/public/uploads/images/424370e1611a171b99b5c6ec20aaeedf_square50.png
                    [filedir] => uploads/images/424370e1611a171b99b5c6ec20aaeedf_square50.png
                    [width] => 50
                    [height] => 50
                    [filesize] => 3683
                )

            [square100] => Array
                (
                    [path] => /Users/matriphe/www/laravel/5.1/public/uploads/images
                    [dir] => uploads/images
                    [filename] => 424370e1611a171b99b5c6ec20aaeedf_square100.png
                    [filepath] => /Users/matriphe/www/laravel/5.1/public/uploads/images/424370e1611a171b99b5c6ec20aaeedf_square100.png
                    [filedir] => uploads/images/424370e1611a171b99b5c6ec20aaeedf_square100.png
                    [width] => 100
                    [height] => 100
                    [filesize] => 10734
                )

            [square200] => Array
                (
                    [path] => /Users/matriphe/www/laravel/5.1/public/uploads/images
                    [dir] => uploads/images
                    [filename] => 424370e1611a171b99b5c6ec20aaeedf_square200.png
                    [filepath] => /Users/matriphe/www/laravel/5.1/public/uploads/images/424370e1611a171b99b5c6ec20aaeedf_square200.png
                    [filedir] => uploads/images/424370e1611a171b99b5c6ec20aaeedf_square200.png
                    [width] => 200
                    [height] => 200
                    [filesize] => 35609
                )

            [square400] => Array
                (
                    [path] => /Users/matriphe/www/laravel/5.1/public/uploads/images
                    [dir] => uploads/images
                    [filename] => 424370e1611a171b99b5c6ec20aaeedf_square400.png
                    [filepath] => /Users/matriphe/www/laravel/5.1/public/uploads/images/424370e1611a171b99b5c6ec20aaeedf_square400.png
                    [filedir] => uploads/images/424370e1611a171b99b5c6ec20aaeedf_square400.png
                    [width] => 400
                    [height] => 400
                    [filesize] => 125267
                )

            [size50] => Array
                (
                    [path] => /Users/matriphe/www/laravel/5.1/public/uploads/images
                    [dir] => uploads/images
                    [filename] => 424370e1611a171b99b5c6ec20aaeedf_size50.png
                    [filepath] => /Users/matriphe/www/laravel/5.1/public/uploads/images/424370e1611a171b99b5c6ec20aaeedf_size50.png
                    [filedir] => uploads/images/424370e1611a171b99b5c6ec20aaeedf_size50.png
                    [width] => 50
                    [height] => 32
                    [filesize] => 2375
                )

            [size100] => Array
                (
                    [path] => /Users/matriphe/www/laravel/5.1/public/uploads/images
                    [dir] => uploads/images
                    [filename] => 424370e1611a171b99b5c6ec20aaeedf_size100.png
                    [filepath] => /Users/matriphe/www/laravel/5.1/public/uploads/images/424370e1611a171b99b5c6ec20aaeedf_size100.png
                    [filedir] => uploads/images/424370e1611a171b99b5c6ec20aaeedf_size100.png
                    [width] => 100
                    [height] => 64
                    [filesize] => 6700
                )

            [size200] => Array
                (
                    [path] => /Users/matriphe/www/laravel/5.1/public/uploads/images
                    [dir] => uploads/images
                    [filename] => 424370e1611a171b99b5c6ec20aaeedf_size200.png
                    [filepath] => /Users/matriphe/www/laravel/5.1/public/uploads/images/424370e1611a171b99b5c6ec20aaeedf_size200.png
                    [filedir] => uploads/images/424370e1611a171b99b5c6ec20aaeedf_size200.png
                    [width] => 200
                    [height] => 127
                    [filesize] => 21432
                )

            [size400] => Array
                (
                    [path] => /Users/matriphe/www/laravel/5.1/public/uploads/images
                    [dir] => uploads/images
                    [filename] => 424370e1611a171b99b5c6ec20aaeedf_size400.png
                    [filepath] => /Users/matriphe/www/laravel/5.1/public/uploads/images/424370e1611a171b99b5c6ec20aaeedf_size400.png
                    [filedir] => uploads/images/424370e1611a171b99b5c6ec20aaeedf_size400.png
                    [width] => 400
                    [height] => 255
                    [filesize] => 76487
                )

        )

)
```
