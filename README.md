# Laravel Imageupload

Upload image easily using Laravel's build in function and resize it using [Intervention Image library](http://image.intervention.io/) automatically. 

The older version was using [Imagine library](https://imagine.readthedocs.org/en/latest/).

## Version Compatibility

 Laravel  | Imageupload | Command
:---------|:------------|:-------
 4.2.x    | [4.2.x](https://github.com/matriphe/laravel-imageupload/blob/laravel42/README.md) | `composer require "matriphe/imageupload:4.2.*"`
 5.0.x / 5.1.x / 5.2.x / 5.3.x / 5.4.x   | [5.x](https://github.com/matriphe/laravel-imageupload/blob/laravel5/README.md) | `composer require "matriphe/imageupload:5.*"`
 5.0.x / 5.1.x / 5.2.x / 5.3.x / 5.4.x    | 6.x | `composer require "matriphe/imageupload:6.*"`

The old version was following Laravel version. Now this package will use semver start from version 6.

## Installation

Open `composer.json` and require this line below.

```json
"matriphe/imageupload": "6.*"
```

Or you can simply run this command from your project directory.

```bash
composer require "matriphe/imageupload"
```

### Laravel 5.x Installation

Open the `config/app.php` and add this line in `providers` section.
```php
Matriphe\Imageupload\ImageuploadServiceProvider::class,
```
Still on `config/app.php` file, add this line in `aliases` section.
```php
'Imageupload' => Matriphe\Imageupload\ImageuploadFacade::class,
```

## Publish Configuration

To control the configuration, you have to *publish* the configuration file.
```bash
php artisan vendor:publish --provider="Matriphe\Imageupload\ImageuploadServiceProvider"
```
After running this command, there will be `config/imageupload.php` file.

## Upload Something

After publishing the configuration file, you can set up a route, view, and start upload something.

The uploaded file will be saved in `public/uploads` directory. Of course, you can change this by publishing and modifying configuration file.

Make sure the directory to store uploaded files is writeable and can be accessed by public.

### Route Example

```php
<?php
// routes.php
...
Route::any('matriphe/imageupload', function() 
{
    $data = [];

    if (Request::hasFile('file')) {
        $data['result'] = Imageupload::upload(Request::file('file'));
    }
    
    return view('form.blade.php')->with($data);
});
```

### View

Add this in your views directory.

```html
<!DOCTYPE html>
<html>
    <head>
        <title>Imageupload</title>
    </head>
    <body>
        <form action="{{ URL::current() }}" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ Session::token() }}">
            <pre>{{ (!empty($result) ? print_r($result, 1) : '') }}</pre>
            <div>
                <input type="file" name="file">
            </div>
            <div>
                <button type="submit">Upload!</button>
            </div>
        </form>
    </body>
</html>
```

## Usage

Just use the `Imageupload::upload(Request::file('file'))` function and it will take care of cropping and renaming. Of course, you can modify on the fly by passing parameter `Imageupload::upload($filesource, $newfilename, $path)`.

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
    [original_filename] => IMG_20170619_195131.jpg
    [original_filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/c074962b5c50b6aa64c360d206bb0ae6.jpg
    [original_filedir] => uploads/images
    [original_extension] => jpg
    [original_mime] => image/jpeg
    [original_filesize] => 1379716
    [original_width] => 2592
    [original_height] => 4608
    [exif] => Array
        (
            [FileName] => c074962b5c50b6aa64c360d206bb0ae6.jpg
            [FileDateTime] => 1500798776
            [FileSize] => 1465208
            [FileType] => 2
            [MimeType] => image/jpeg
            [SectionsFound] => COMMENT
            [COMPUTED] => Array
                (
                    [html] => width="2592" height="4608"
                    [Height] => 4608
                    [Width] => 2592
                    [IsColor] => 1
                )

            [COMMENT] => Array
                (
                    [0] => CREATOR: gd-jpeg v1.0 (using IJG JPEG v80), quality = 90

                )

        )

    [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
    [dir] => uploads
    [filename] => c074962b5c50b6aa64c360d206bb0ae6.jpg
    [basename] => c074962b5c50b6aa64c360d206bb0ae6
    [dimensions] => Array
        (
            [square50] => Array
                (
                    [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                    [dir] => uploads/images
                    [filename] => c074962b5c50b6aa64c360d206bb0ae6_square50.jpg
                    [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/c074962b5c50b6aa64c360d206bb0ae6_square50.jpg
                    [filedir] => uploads/images
                    [width] => 50
                    [height] => 50
                    [filesize] => 1725
                    [is_squared] => 1
                )

            [square100] => Array
                (
                    [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                    [dir] => uploads/images
                    [filename] => c074962b5c50b6aa64c360d206bb0ae6_square100.jpg
                    [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/c074962b5c50b6aa64c360d206bb0ae6_square100.jpg
                    [filedir] => uploads/images
                    [width] => 100
                    [height] => 100
                    [filesize] => 3759
                    [is_squared] => 1
                )

            [square200] => Array
                (
                    [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                    [dir] => uploads/images
                    [filename] => c074962b5c50b6aa64c360d206bb0ae6_square200.jpg
                    [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/c074962b5c50b6aa64c360d206bb0ae6_square200.jpg
                    [filedir] => uploads/images
                    [width] => 200
                    [height] => 200
                    [filesize] => 9924
                    [is_squared] => 1
                )

            [square400] => Array
                (
                    [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                    [dir] => uploads/images
                    [filename] => c074962b5c50b6aa64c360d206bb0ae6_square400.jpg
                    [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/c074962b5c50b6aa64c360d206bb0ae6_square400.jpg
                    [filedir] => uploads/images
                    [width] => 400
                    [height] => 400
                    [filesize] => 28406
                    [is_squared] => 1
                )

            [size50] => Array
                (
                    [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                    [dir] => uploads/images
                    [filename] => c074962b5c50b6aa64c360d206bb0ae6_size50.jpg
                    [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/c074962b5c50b6aa64c360d206bb0ae6_size50.jpg
                    [filedir] => uploads/images
                    [width] => 28
                    [height] => 50
                    [filesize] => 1304
                    [is_squared] => 
                )

            [size100] => Array
                (
                    [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                    [dir] => uploads/images
                    [filename] => c074962b5c50b6aa64c360d206bb0ae6_size100.jpg
                    [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/c074962b5c50b6aa64c360d206bb0ae6_size100.jpg
                    [filedir] => uploads/images
                    [width] => 56
                    [height] => 100
                    [filesize] => 2504
                    [is_squared] => 
                )

            [size200] => Array
                (
                    [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                    [dir] => uploads/images
                    [filename] => c074962b5c50b6aa64c360d206bb0ae6_size200.jpg
                    [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/c074962b5c50b6aa64c360d206bb0ae6_size200.jpg
                    [filedir] => uploads/images
                    [width] => 112
                    [height] => 200
                    [filesize] => 6324
                    [is_squared] => 
                )

            [size400] => Array
                (
                    [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                    [dir] => uploads/images
                    [filename] => c074962b5c50b6aa64c360d206bb0ae6_size400.jpg
                    [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/c074962b5c50b6aa64c360d206bb0ae6_size400.jpg
                    [filedir] => uploads/images
                    [width] => 225
                    [height] => 400
                    [filesize] => 19123
                    [is_squared] => 
                )

        )

)
```

## Next Feature

 * Utilize Laravel's filesystem to store uploaded file.
 * Add Lumen support
 
## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.