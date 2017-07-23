# Laravel Imageupload

[![Build Status](https://travis-ci.org/matriphe/laravel-imageupload.svg?branch=master)](https://travis-ci.org/matriphe/laravel-imageupload)

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
    [original_filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/aadecb1e623b1a3fd1ae3c1ad4137e96.jpg
    [original_filedir] => uploads/images
    [original_extension] => jpg
    [original_mime] => image/jpeg
    [original_filesize] => 1379716
    [original_width] => 2592
    [original_height] => 4608
    [exif] => Array
        (
            [FileName] => phpAzVzkv
            [FileDateTime] => 1500799366
            [FileSize] => 1379716
            [FileType] => 2
            [MimeType] => image/jpeg
            [SectionsFound] => ANY_TAG, IFD0, THUMBNAIL, EXIF, GPS, INTEROP
            [COMPUTED] => Array
                (
                    [html] => width="2592" height="4608"
                    [Height] => 4608
                    [Width] => 2592
                    [IsColor] => 1
                    [ByteOrderMotorola] => 1
                    [ApertureFNumber] => f/2.0
                    [Thumbnail.FileType] => 2
                    [Thumbnail.MimeType] => image/jpeg
                )

            [Make] => Xiaomi
            [Model] => Redmi Note3
            [XResolution] => 72/1
            [YResolution] => 72/1
            [ResolutionUnit] => 2
            [Software] => kenzo-user 6.0.1 MMB29M 7.6.7 release-keys
            [DateTime] => 2017:06:19 19:51:31
            [YCbCrPositioning] => 1
            [Exif_IFD_Pointer] => 234
            [GPS_IFD_Pointer] => 718
            [THUMBNAIL] => Array
                (
                    [Compression] => 6
                    [XResolution] => 72/1
                    [YResolution] => 72/1
                    [ResolutionUnit] => 2
                    [JPEGInterchangeFormat] => 898
                    [JPEGInterchangeFormatLength] => 15696
                )

            [ExposureTime] => 1/33
            [FNumber] => 200/100
            [ExposureProgram] => 0
            [ISOSpeedRatings] => 854
            [ExifVersion] => 0220
            [DateTimeOriginal] => 2017:06:19 19:51:31
            [DateTimeDigitized] => 2017:06:19 19:51:31
            [ComponentsConfiguration] => 
            [ShutterSpeedValue] => 5058/1000
            [ApertureValue] => 200/100
            [BrightnessValue] => 300/100
            [MeteringMode] => 1
            [Flash] => 16
            [FocalLength] => 357/100
            [SubSecTime] => 123298
            [SubSecTimeOriginal] => 123298
            [SubSecTimeDigitized] => 123298
            [FlashPixVersion] => 0100
            [ColorSpace] => 1
            [ExifImageWidth] => 2592
            [ExifImageLength] => 4608
            [InteroperabilityOffset] => 687
            [SensingMethod] => 2
            [SceneType] => 
            [ExposureMode] => 0
            [WhiteBalance] => 0
            [FocalLengthIn35mmFilm] => 4
            [SceneCaptureType] => 0
            [GPSAltitudeRef] => 200/100
            [GPSTimeStamp] => Array
                (
                    [0] => 12/1
                    [1] => 51/1
                    [2] => 30/1
                )

            [GPSDateStamp] => 2017:06:19
            [InterOperabilityIndex] => R98
            [InterOperabilityVersion] => 0100
        )

    [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
    [dir] => uploads
    [filename] => aadecb1e623b1a3fd1ae3c1ad4137e96.jpg
    [basename] => aadecb1e623b1a3fd1ae3c1ad4137e96
    [dimensions] => Array
        (
            [square50] => Array
                (
                    [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                    [dir] => uploads/images
                    [filename] => aadecb1e623b1a3fd1ae3c1ad4137e96_square50.jpg
                    [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/aadecb1e623b1a3fd1ae3c1ad4137e96_square50.jpg
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
                    [filename] => aadecb1e623b1a3fd1ae3c1ad4137e96_square100.jpg
                    [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/aadecb1e623b1a3fd1ae3c1ad4137e96_square100.jpg
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
                    [filename] => aadecb1e623b1a3fd1ae3c1ad4137e96_square200.jpg
                    [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/aadecb1e623b1a3fd1ae3c1ad4137e96_square200.jpg
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
                    [filename] => aadecb1e623b1a3fd1ae3c1ad4137e96_square400.jpg
                    [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/aadecb1e623b1a3fd1ae3c1ad4137e96_square400.jpg
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
                    [filename] => aadecb1e623b1a3fd1ae3c1ad4137e96_size50.jpg
                    [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/aadecb1e623b1a3fd1ae3c1ad4137e96_size50.jpg
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
                    [filename] => aadecb1e623b1a3fd1ae3c1ad4137e96_size100.jpg
                    [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/aadecb1e623b1a3fd1ae3c1ad4137e96_size100.jpg
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
                    [filename] => aadecb1e623b1a3fd1ae3c1ad4137e96_size200.jpg
                    [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/aadecb1e623b1a3fd1ae3c1ad4137e96_size200.jpg
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
                    [filename] => aadecb1e623b1a3fd1ae3c1ad4137e96_size400.jpg
                    [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/aadecb1e623b1a3fd1ae3c1ad4137e96_size400.jpg
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