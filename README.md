# Laravel Imageupload

[![Build Status](https://travis-ci.org/matriphe/laravel-imageupload.svg?branch=master)](https://travis-ci.org/matriphe/laravel-imageupload)
[![Total Download](https://img.shields.io/packagist/dt/matriphe/imageupload.svg)](https://packagist.org/packages/matriphe/imageupload)
[![Latest Stable Version](https://img.shields.io/packagist/v/matriphe/imageupload.svg)](https://packagist.org/packages/matriphe/imageupload)


Upload image easily using Laravel's build in function and resize it automatically. 

---

  * [Version Compatibility](#version-compatibility)
  * [Installation](#installation)
    * [Laravel 5\.x Installation](#laravel-5x-installation)
  * [Publish Configuration and Migration File](#publish-configuration-and-migration-file)
    * [Configuration](#configuration)
    * [Migration](#migration)
    * [Model](#model)
  * [Try Upload Something\!](#try-upload-something)
    * [Route Example](#route-example)
    * [View](#view)
  * [Usage](#usage)
    * [Set Output](#set-output)
    * [Example](#example)
    * [Output Example](#output-example)
      * [JSON](#json)
      * [Array](#array)
      * [Collection](#collection)
      * [ImageuploadModel](#imageuploadmodel)
  * [Changelog](#changelog)
    * [Version 6\.x](#version-6x)
    * [Version 5\.x and 4\.2\.x](#version-5x-and-42x)
  * [Next Feature](#next-feature)
  * [License](#license)

---

## Version Compatibility

 Laravel  | Imageupload | Installation Command
:---------|:------------|:--------------------
 4.2.x    | [4.x](https://github.com/matriphe/laravel-imageupload/blob/laravel42/README.md) (obsolete) | `composer require "matriphe/imageupload:4.2.*"`
 5.0.x / 5.1.x / 5.2.x / 5.3.x / 5.4.x   | [5.x](https://github.com/matriphe/laravel-imageupload/blob/laravel5/README.md) (stable) | `composer require "matriphe/imageupload:5.*"`
 5.0.x / 5.1.x / 5.2.x / 5.3.x / 5.4.x    | 6.0 | `composer require "matriphe/imageupload:6.0"`
 5.2.x / 5.3.x / 5.4.x    | 6.1.x (latest) | `composer require "matriphe/imageupload:6.1.*"`

The old version was following Laravel version. Now this package will use [semantic version (semver)](http://semver.org/) start from version 6.0.

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

## Publish Configuration and Migration File

To control the configuration, you have to publish the configuration file.

```bash
php artisan vendor:publish --provider="Matriphe\Imageupload\ImageuploadServiceProvider"
```

After running this command, there will be `config/imageupload.php` config file and `database/migrations/2017_07_24_024410_create_image_upload_table.php` migration file.

### Configuration

Please check the `config/imageupload.php` for more detail. You can use `.env` to config based on your environment.

If you want to publish the configuration file only, run this command.

```bash
php artisan vendor:publish --provider="Matriphe\Imageupload\ImageuploadServiceProvider" --tag=config
```

### Migration

By default, a migration file will create `image_uploads` table. Check the file and modify to fit your need.

If you want to publish the migration file only, run this command.

```bash
php artisan vendor:publish --provider="Matriphe\Imageupload\ImageuploadServiceProvider --tag=migrations"
```

### Model

You can create a model to extend the built-in model, by extending `Matriphe\Imageupload\ImageuploadModel`. Please check this file too and adjust to fit your need.

```php
<?php

namespace App;

use Matriphe\Imageupload\ImageuploadModel;

class Image extends ImageuploadModel
{
    protected $table = 'images';
}

```

## Try Upload Something!

After publishing the configuration file, you can set up a route, view, and start upload something.

The uploaded file will be saved in `public/uploads` directory. Of course, you can change this by publishing and modifying configuration file.

Make sure the directory to store uploaded files is writable and can be accessed by public.

### Route Example

```php
<?php
// routes.php
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

The return of the function is instance of `Illuminate\Support\Collection`. You can easily convert to array or JSON by using `toArray()` or `toJson()` method.

### Set Output

To change the output on fly, use method `ouput($output)` before calling `upload($request)`. The options is `collection`, `json`, `db`, and `array` (default). See the config file to set the default output.

The `db` option will automatically save output to database and return `Matriphe\Imageupload\ImageuploadModel` object.

### Example

```php
if (Request::hasFile('file')) {
    $result = Imageupload::upload(Request::file('file'));
}

if (Request::hasFile('file')) {
    $result = Imageupload::output('json')->upload(Request::file('file'));
}
```

### Output Example

#### JSON

```json
{  
    "original_filename":"IMG_20170619_195131.jpg",
    "original_filepath":"\/Volumes\/data\/Development\/php\/laravel\/51\/public\/uploads\/images\/IMG_20170619_195131.jpg",
    "original_filedir":"uploads\/images\/IMG_20170619_195131.jpg",
    "original_extension":"jpg",
    "original_mime":"image\/jpeg",
    "original_filesize":1379716,
    "original_width":2592,
    "original_height":4608,
    "exif":{  
        "FileName":"phpPfn2JP",
        "FileDateTime":1500894790,
        "FileSize":1379716,
        "FileType":2,
        "MimeType":"image\/jpeg",
        "SectionsFound":"ANY_TAG, IFD0, THUMBNAIL, EXIF, GPS, INTEROP",
        "COMPUTED":{  
            "html":"width=\"2592\" height=\"4608\"",
            "Height":4608,
            "Width":2592,
            "IsColor":1,
            "ByteOrderMotorola":1,
            "ApertureFNumber":"f\/2.0",
            "Thumbnail.FileType":2,
            "Thumbnail.MimeType":"image\/jpeg"
        },
        "Make":"Xiaomi",
        "Model":"Redmi Note3",
        "XResolution":"72\/1",
        "YResolution":"72\/1",
        "ResolutionUnit":2,
        "Software":"kenzo-user 6.0.1 MMB29M 7.6.7 release-keys",
        "DateTime":"2017:06:19 19:51:31",
        "YCbCrPositioning":1,
        "Exif_IFD_Pointer":234,
        "GPS_IFD_Pointer":718,
        "THUMBNAIL":{  
            "Compression":6,
            "XResolution":"72\/1",
            "YResolution":"72\/1",
            "ResolutionUnit":2,
            "JPEGInterchangeFormat":898,
            "JPEGInterchangeFormatLength":15696
        },
        "ExposureTime":"1\/33",
        "FNumber":"200\/100",
        "ExposureProgram":0,
        "ISOSpeedRatings":854,
        "ExifVersion":"0220",
        "DateTimeOriginal":"2017:06:19 19:51:31",
        "DateTimeDigitized":"2017:06:19 19:51:31",
        "ComponentsConfiguration":"\u0001\u0002\u0003\u0000",
        "ShutterSpeedValue":"5058\/1000",
        "ApertureValue":"200\/100",
        "BrightnessValue":"300\/100",
        "MeteringMode":1,
        "Flash":16,
        "FocalLength":"357\/100",
        "SubSecTime":"123298",
        "SubSecTimeOriginal":"123298",
        "SubSecTimeDigitized":"123298",
        "FlashPixVersion":"0100",
        "ColorSpace":1,
        "ExifImageWidth":2592,
        "ExifImageLength":4608,
        "InteroperabilityOffset":687,
        "SensingMethod":2,
        "SceneType":"\u0001",
        "ExposureMode":0,
        "WhiteBalance":0,
        "FocalLengthIn35mmFilm":4,
        "SceneCaptureType":0,
        "GPSAltitudeRef":"200\/100",
        "GPSTimeStamp":[  
            "12\/1",
            "51\/1",
            "30\/1"
        ],
        "GPSDateStamp":"2017:06:19",
        "InterOperabilityIndex":"R98",
        "InterOperabilityVersion":"0100"
    },
    "path":"\/Volumes\/data\/Development\/php\/laravel\/51\/public\/uploads\/images",
    "dir":"uploads\/images",
    "filename":"IMG_20170619_195131.jpg",
    "basename":"IMG_20170619_195131",
    "dimensions":{  
        "square50":{  
            "path":"\/Volumes\/data\/Development\/php\/laravel\/51\/public\/uploads\/images",
            "dir":"uploads\/images\/IMG_20170619_195131_square50.jpg",
            "filename":"IMG_20170619_195131_square50.jpg",
            "filepath":"\/Volumes\/data\/Development\/php\/laravel\/51\/public\/uploads\/images\/IMG_20170619_195131_square50.jpg",
            "filedir":"uploads\/images\/IMG_20170619_195131_square50.jpg",
            "width":50,
            "height":50,
            "filesize":1379716,
            "is_squared":true
        },
        "square100":{  
            "path":"\/Volumes\/data\/Development\/php\/laravel\/51\/public\/uploads\/images",
            "dir":"uploads\/images\/IMG_20170619_195131_square100.jpg",
            "filename":"IMG_20170619_195131_square100.jpg",
            "filepath":"\/Volumes\/data\/Development\/php\/laravel\/51\/public\/uploads\/images\/IMG_20170619_195131_square100.jpg",
            "filedir":"uploads\/images\/IMG_20170619_195131_square100.jpg",
            "width":100,
            "height":100,
            "filesize":1379716,
            "is_squared":true
        },
        "square200":{  
            "path":"\/Volumes\/data\/Development\/php\/laravel\/51\/public\/uploads\/images",
            "dir":"uploads\/images\/IMG_20170619_195131_square200.jpg",
            "filename":"IMG_20170619_195131_square200.jpg",
            "filepath":"\/Volumes\/data\/Development\/php\/laravel\/51\/public\/uploads\/images\/IMG_20170619_195131_square200.jpg",
            "filedir":"uploads\/images\/IMG_20170619_195131_square200.jpg",
            "width":200,
            "height":200,
            "filesize":1379716,
            "is_squared":true
        },
        "square400":{  
            "path":"\/Volumes\/data\/Development\/php\/laravel\/51\/public\/uploads\/images",
            "dir":"uploads\/images\/IMG_20170619_195131_square400.jpg",
            "filename":"IMG_20170619_195131_square400.jpg",
            "filepath":"\/Volumes\/data\/Development\/php\/laravel\/51\/public\/uploads\/images\/IMG_20170619_195131_square400.jpg",
            "filedir":"uploads\/images\/IMG_20170619_195131_square400.jpg",
            "width":400,
            "height":400,
            "filesize":1379716,
            "is_squared":true
        },
        "size50":{  
            "path":"\/Volumes\/data\/Development\/php\/laravel\/51\/public\/uploads\/images",
            "dir":"uploads\/images\/IMG_20170619_195131_size50.jpg",
            "filename":"IMG_20170619_195131_size50.jpg",
            "filepath":"\/Volumes\/data\/Development\/php\/laravel\/51\/public\/uploads\/images\/IMG_20170619_195131_size50.jpg",
            "filedir":"uploads\/images\/IMG_20170619_195131_size50.jpg",
            "width":28,
            "height":50,
            "filesize":1379716,
            "is_squared":false
        },
        "size100":{  
            "path":"\/Volumes\/data\/Development\/php\/laravel\/51\/public\/uploads\/images",
            "dir":"uploads\/images\/IMG_20170619_195131_size100.jpg",
            "filename":"IMG_20170619_195131_size100.jpg",
            "filepath":"\/Volumes\/data\/Development\/php\/laravel\/51\/public\/uploads\/images\/IMG_20170619_195131_size100.jpg",
            "filedir":"uploads\/images\/IMG_20170619_195131_size100.jpg",
            "width":56,
            "height":100,
            "filesize":1379716,
            "is_squared":false
        },
        "size200":{  
            "path":"\/Volumes\/data\/Development\/php\/laravel\/51\/public\/uploads\/images",
            "dir":"uploads\/images\/IMG_20170619_195131_size200.jpg",
            "filename":"IMG_20170619_195131_size200.jpg",
            "filepath":"\/Volumes\/data\/Development\/php\/laravel\/51\/public\/uploads\/images\/IMG_20170619_195131_size200.jpg",
            "filedir":"uploads\/images\/IMG_20170619_195131_size200.jpg",
            "width":112,
            "height":200,
            "filesize":1379716,
            "is_squared":false
        },
        "size400":{  
            "path":"\/Volumes\/data\/Development\/php\/laravel\/51\/public\/uploads\/images",
            "dir":"uploads\/images\/IMG_20170619_195131_size400.jpg",
            "filename":"IMG_20170619_195131_size400.jpg",
            "filepath":"\/Volumes\/data\/Development\/php\/laravel\/51\/public\/uploads\/images\/IMG_20170619_195131_size400.jpg",
            "filedir":"uploads\/images\/IMG_20170619_195131_size400.jpg",
            "width":225,
            "height":400,
            "filesize":1379716,
            "is_squared":false
        }
    }
}
```

#### Array

```array
Array
(
    [original_filename] => IMG_20170619_195131.jpg
    [original_filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/IMG_20170619_195131.jpg
    [original_filedir] => uploads/images/IMG_20170619_195131.jpg
    [original_extension] => jpg
    [original_mime] => image/jpeg
    [original_filesize] => 1379716
    [original_width] => 2592
    [original_height] => 4608
    [exif] => Array
        (
            [FileName] => phpPfn2JP
            [FileDateTime] => 1500894790
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
    [dir] => uploads/images
    [filename] => IMG_20170619_195131.jpg
    [basename] => IMG_20170619_195131
    [dimensions] => Array
        (
            [square50] => Array
                (
                    [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                    [dir] => uploads/images/IMG_20170619_195131_square50.jpg
                    [filename] => IMG_20170619_195131_square50.jpg
                    [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/IMG_20170619_195131_square50.jpg
                    [filedir] => uploads/images/IMG_20170619_195131_square50.jpg
                    [width] => 50
                    [height] => 50
                    [filesize] => 1379716
                    [is_squared] => 1
                )

            [square100] => Array
                (
                    [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                    [dir] => uploads/images/IMG_20170619_195131_square100.jpg
                    [filename] => IMG_20170619_195131_square100.jpg
                    [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/IMG_20170619_195131_square100.jpg
                    [filedir] => uploads/images/IMG_20170619_195131_square100.jpg
                    [width] => 100
                    [height] => 100
                    [filesize] => 1379716
                    [is_squared] => 1
                )

            [square200] => Array
                (
                    [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                    [dir] => uploads/images/IMG_20170619_195131_square200.jpg
                    [filename] => IMG_20170619_195131_square200.jpg
                    [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/IMG_20170619_195131_square200.jpg
                    [filedir] => uploads/images/IMG_20170619_195131_square200.jpg
                    [width] => 200
                    [height] => 200
                    [filesize] => 1379716
                    [is_squared] => 1
                )

            [square400] => Array
                (
                    [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                    [dir] => uploads/images/IMG_20170619_195131_square400.jpg
                    [filename] => IMG_20170619_195131_square400.jpg
                    [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/IMG_20170619_195131_square400.jpg
                    [filedir] => uploads/images/IMG_20170619_195131_square400.jpg
                    [width] => 400
                    [height] => 400
                    [filesize] => 1379716
                    [is_squared] => 1
                )

            [size50] => Array
                (
                    [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                    [dir] => uploads/images/IMG_20170619_195131_size50.jpg
                    [filename] => IMG_20170619_195131_size50.jpg
                    [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/IMG_20170619_195131_size50.jpg
                    [filedir] => uploads/images/IMG_20170619_195131_size50.jpg
                    [width] => 28
                    [height] => 50
                    [filesize] => 1379716
                    [is_squared] => 
                )

            [size100] => Array
                (
                    [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                    [dir] => uploads/images/IMG_20170619_195131_size100.jpg
                    [filename] => IMG_20170619_195131_size100.jpg
                    [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/IMG_20170619_195131_size100.jpg
                    [filedir] => uploads/images/IMG_20170619_195131_size100.jpg
                    [width] => 56
                    [height] => 100
                    [filesize] => 1379716
                    [is_squared] => 
                )

            [size200] => Array
                (
                    [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                    [dir] => uploads/images/IMG_20170619_195131_size200.jpg
                    [filename] => IMG_20170619_195131_size200.jpg
                    [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/IMG_20170619_195131_size200.jpg
                    [filedir] => uploads/images/IMG_20170619_195131_size200.jpg
                    [width] => 112
                    [height] => 200
                    [filesize] => 1379716
                    [is_squared] => 
                )

            [size400] => Array
                (
                    [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                    [dir] => uploads/images/IMG_20170619_195131_size400.jpg
                    [filename] => IMG_20170619_195131_size400.jpg
                    [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/IMG_20170619_195131_size400.jpg
                    [filedir] => uploads/images/IMG_20170619_195131_size400.jpg
                    [width] => 225
                    [height] => 400
                    [filesize] => 1379716
                    [is_squared] => 
                )

        )

)
```

#### Collection

```php
Illuminate\Support\Collection Object
(
    [items:protected] => Array
        (
            [original_filename] => IMG_20170619_195131.jpg
            [original_filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/IMG_20170619_195131.jpg
            [original_filedir] => uploads/images/IMG_20170619_195131.jpg
            [original_extension] => jpg
            [original_mime] => image/jpeg
            [original_filesize] => 1379716
            [original_width] => 2592
            [original_height] => 4608
            [exif] => Array
                (
                    [FileName] => phpGacSlt
                    [FileDateTime] => 1500895792
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
            [dir] => uploads/images
            [filename] => IMG_20170619_195131.jpg
            [basename] => IMG_20170619_195131
            [dimensions] => Array
                (
                    [square50] => Array
                        (
                            [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                            [dir] => uploads/images/IMG_20170619_195131_square50.jpg
                            [filename] => IMG_20170619_195131_square50.jpg
                            [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/IMG_20170619_195131_square50.jpg
                            [filedir] => uploads/images/IMG_20170619_195131_square50.jpg
                            [width] => 50
                            [height] => 50
                            [filesize] => 1379716
                            [is_squared] => 1
                        )

                    [square100] => Array
                        (
                            [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                            [dir] => uploads/images/IMG_20170619_195131_square100.jpg
                            [filename] => IMG_20170619_195131_square100.jpg
                            [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/IMG_20170619_195131_square100.jpg
                            [filedir] => uploads/images/IMG_20170619_195131_square100.jpg
                            [width] => 100
                            [height] => 100
                            [filesize] => 1379716
                            [is_squared] => 1
                        )

                    [square200] => Array
                        (
                            [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                            [dir] => uploads/images/IMG_20170619_195131_square200.jpg
                            [filename] => IMG_20170619_195131_square200.jpg
                            [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/IMG_20170619_195131_square200.jpg
                            [filedir] => uploads/images/IMG_20170619_195131_square200.jpg
                            [width] => 200
                            [height] => 200
                            [filesize] => 1379716
                            [is_squared] => 1
                        )

                    [square400] => Array
                        (
                            [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                            [dir] => uploads/images/IMG_20170619_195131_square400.jpg
                            [filename] => IMG_20170619_195131_square400.jpg
                            [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/IMG_20170619_195131_square400.jpg
                            [filedir] => uploads/images/IMG_20170619_195131_square400.jpg
                            [width] => 400
                            [height] => 400
                            [filesize] => 1379716
                            [is_squared] => 1
                        )

                    [size50] => Array
                        (
                            [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                            [dir] => uploads/images/IMG_20170619_195131_size50.jpg
                            [filename] => IMG_20170619_195131_size50.jpg
                            [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/IMG_20170619_195131_size50.jpg
                            [filedir] => uploads/images/IMG_20170619_195131_size50.jpg
                            [width] => 28
                            [height] => 50
                            [filesize] => 1379716
                            [is_squared] => 
                        )

                    [size100] => Array
                        (
                            [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                            [dir] => uploads/images/IMG_20170619_195131_size100.jpg
                            [filename] => IMG_20170619_195131_size100.jpg
                            [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/IMG_20170619_195131_size100.jpg
                            [filedir] => uploads/images/IMG_20170619_195131_size100.jpg
                            [width] => 56
                            [height] => 100
                            [filesize] => 1379716
                            [is_squared] => 
                        )

                    [size200] => Array
                        (
                            [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                            [dir] => uploads/images/IMG_20170619_195131_size200.jpg
                            [filename] => IMG_20170619_195131_size200.jpg
                            [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/IMG_20170619_195131_size200.jpg
                            [filedir] => uploads/images/IMG_20170619_195131_size200.jpg
                            [width] => 112
                            [height] => 200
                            [filesize] => 1379716
                            [is_squared] => 
                        )

                    [size400] => Array
                        (
                            [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
                            [dir] => uploads/images/IMG_20170619_195131_size400.jpg
                            [filename] => IMG_20170619_195131_size400.jpg
                            [filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/IMG_20170619_195131_size400.jpg
                            [filedir] => uploads/images/IMG_20170619_195131_size400.jpg
                            [width] => 225
                            [height] => 400
                            [filesize] => 1379716
                            [is_squared] => 
                        )

                )

        )

)
```

#### ImageuploadModel

```php
Matriphe\Imageupload\ImageuploadModel Object
(
    [thumbnailKeys:protected] => Array
        (
            [0] => path
            [1] => dir
            [2] => filename
            [3] => filepath
            [4] => filedir
            [5] => width
            [6] => height
            [7] => filesize
        )

    [fillable:protected] => Array
        (
            [0] => original_filename
            [1] => original_filepath
            [2] => original_filedir
            [3] => original_extension
            [4] => original_mime
            [5] => original_filesize
            [6] => original_width
            [7] => original_height
            [8] => path
            [9] => dir
            [10] => filename
            [11] => basename
            [12] => exif
        )

    [connection:protected] => 
    [table:protected] => image_uploads
    [primaryKey:protected] => id
    [perPage:protected] => 15
    [incrementing] => 1
    [timestamps] => 1
    [attributes:protected] => Array
        (
            [original_filename] => IMG_20170619_195131.jpg
            [original_filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/IMG_20170619_195131.jpg
            [original_filedir] => uploads/images/IMG_20170619_195131.jpg
            [original_extension] => jpg
            [original_mime] => image/jpeg
            [original_filesize] => 1379716
            [original_width] => 2592
            [original_height] => 4608
            [exif] => {"FileName":"php19qj3X","FileDateTime":1500906046,"FileSize":1379716,"FileType":2,"MimeType":"image\/jpeg","SectionsFound":"ANY_TAG, IFD0, THUMBNAIL, EXIF, GPS, INTEROP","COMPUTED":{"html":"width=\"2592\" height=\"4608\"","Height":4608,"Width":2592,"IsColor":1,"ByteOrderMotorola":1,"ApertureFNumber":"f\/2.0","Thumbnail.FileType":2,"Thumbnail.MimeType":"image\/jpeg"},"Make":"Xiaomi","Model":"Redmi Note3","XResolution":"72\/1","YResolution":"72\/1","ResolutionUnit":2,"Software":"kenzo-user 6.0.1 MMB29M 7.6.7 release-keys","DateTime":"2017:06:19 19:51:31","YCbCrPositioning":1,"Exif_IFD_Pointer":234,"GPS_IFD_Pointer":718,"THUMBNAIL":{"Compression":6,"XResolution":"72\/1","YResolution":"72\/1","ResolutionUnit":2,"JPEGInterchangeFormat":898,"JPEGInterchangeFormatLength":15696},"ExposureTime":"1\/33","FNumber":"200\/100","ExposureProgram":0,"ISOSpeedRatings":854,"ExifVersion":"0220","DateTimeOriginal":"2017:06:19 19:51:31","DateTimeDigitized":"2017:06:19 19:51:31","ComponentsConfiguration":"\u0001\u0002\u0003\u0000","ShutterSpeedValue":"5058\/1000","ApertureValue":"200\/100","BrightnessValue":"300\/100","MeteringMode":1,"Flash":16,"FocalLength":"357\/100","SubSecTime":"123298","SubSecTimeOriginal":"123298","SubSecTimeDigitized":"123298","FlashPixVersion":"0100","ColorSpace":1,"ExifImageWidth":2592,"ExifImageLength":4608,"InteroperabilityOffset":687,"SensingMethod":2,"SceneType":"\u0001","ExposureMode":0,"WhiteBalance":0,"FocalLengthIn35mmFilm":4,"SceneCaptureType":0,"GPSAltitudeRef":"200\/100","GPSTimeStamp":["12\/1","51\/1","30\/1"],"GPSDateStamp":"2017:06:19","InterOperabilityIndex":"R98","InterOperabilityVersion":"0100"}
            [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
            [dir] => uploads/images
            [filename] => IMG_20170619_195131.jpg
            [basename] => IMG_20170619_195131
            [updated_at] => 2017-07-24 21:20:53
            [created_at] => 2017-07-24 21:20:53
            [id] => 1
        )

    [original:protected] => Array
        (
            [original_filename] => IMG_20170619_195131.jpg
            [original_filepath] => /Volumes/data/Development/php/laravel/51/public/uploads/images/IMG_20170619_195131.jpg
            [original_filedir] => uploads/images/IMG_20170619_195131.jpg
            [original_extension] => jpg
            [original_mime] => image/jpeg
            [original_filesize] => 1379716
            [original_width] => 2592
            [original_height] => 4608
            [exif] => {"FileName":"php19qj3X","FileDateTime":1500906046,"FileSize":1379716,"FileType":2,"MimeType":"image\/jpeg","SectionsFound":"ANY_TAG, IFD0, THUMBNAIL, EXIF, GPS, INTEROP","COMPUTED":{"html":"width=\"2592\" height=\"4608\"","Height":4608,"Width":2592,"IsColor":1,"ByteOrderMotorola":1,"ApertureFNumber":"f\/2.0","Thumbnail.FileType":2,"Thumbnail.MimeType":"image\/jpeg"},"Make":"Xiaomi","Model":"Redmi Note3","XResolution":"72\/1","YResolution":"72\/1","ResolutionUnit":2,"Software":"kenzo-user 6.0.1 MMB29M 7.6.7 release-keys","DateTime":"2017:06:19 19:51:31","YCbCrPositioning":1,"Exif_IFD_Pointer":234,"GPS_IFD_Pointer":718,"THUMBNAIL":{"Compression":6,"XResolution":"72\/1","YResolution":"72\/1","ResolutionUnit":2,"JPEGInterchangeFormat":898,"JPEGInterchangeFormatLength":15696},"ExposureTime":"1\/33","FNumber":"200\/100","ExposureProgram":0,"ISOSpeedRatings":854,"ExifVersion":"0220","DateTimeOriginal":"2017:06:19 19:51:31","DateTimeDigitized":"2017:06:19 19:51:31","ComponentsConfiguration":"\u0001\u0002\u0003\u0000","ShutterSpeedValue":"5058\/1000","ApertureValue":"200\/100","BrightnessValue":"300\/100","MeteringMode":1,"Flash":16,"FocalLength":"357\/100","SubSecTime":"123298","SubSecTimeOriginal":"123298","SubSecTimeDigitized":"123298","FlashPixVersion":"0100","ColorSpace":1,"ExifImageWidth":2592,"ExifImageLength":4608,"InteroperabilityOffset":687,"SensingMethod":2,"SceneType":"\u0001","ExposureMode":0,"WhiteBalance":0,"FocalLengthIn35mmFilm":4,"SceneCaptureType":0,"GPSAltitudeRef":"200\/100","GPSTimeStamp":["12\/1","51\/1","30\/1"],"GPSDateStamp":"2017:06:19","InterOperabilityIndex":"R98","InterOperabilityVersion":"0100"}
            [path] => /Volumes/data/Development/php/laravel/51/public/uploads/images
            [dir] => uploads/images
            [filename] => IMG_20170619_195131.jpg
            [basename] => IMG_20170619_195131
            [updated_at] => 2017-07-24 21:20:53
            [created_at] => 2017-07-24 21:20:53
            [id] => 1
        )

    [relations:protected] => Array
        (
        )

    [hidden:protected] => Array
        (
        )

    [visible:protected] => Array
        (
        )

    [appends:protected] => Array
        (
        )

    [guarded:protected] => Array
        (
            [0] => *
        )

    [dates:protected] => Array
        (
        )

    [dateFormat:protected] => 
    [casts:protected] => Array
        (
        )

    [touches:protected] => Array
        (
        )

    [observables:protected] => Array
        (
        )

    [with:protected] => Array
        (
        )

    [morphClass:protected] => 
    [exists] => 1
    [wasRecentlyCreated] => 1
)
```


## Changelog

### Version 6.1

* Support output type `json`, `array`, `collection`, and `db`
* Add support to change output type using `output()` method
* Remove support for Laravel 5.0 and 5.1

### Version 6.0

* Start using [semantic version (semver)](http://semver.org/) for versioning
* Using [Intervention](http://image.intervention.io/use/basics) for image processing
* Support only [GD](https://libgd.github.io/) and [Imagick (ImageMagick)](http://php.net/manual/en/book.imagick.php), [Gmagick](http://php.net/manual/en/book.gmagick.php) is not supported anymore
* Using exception for better error handling

### Version 5.x and 4.2.x

* Last version that follow Laravel versioning
* Last version that use [Imagine library](https://imagine.readthedocs.org/en/latest/)

## Next Feature

 * Utilize Laravel's filesystem to store uploaded file.
 * Add Lumen support
 
## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.