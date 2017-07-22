<?php

namespace Matriphe\Imageupload;

use Config;
use Exception;
use File;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Imageupload
{
    /**
     * The results
     *
     * @var array
     * @access public
     */
    public $results = [
        'original_filename' => null,
        'original_filepath' => null,
        'original_filedir' => null,
        'original_extension' => null,
        'original_mime' => null,
        'original_filesize' => 0,
        'original_width' => 0,
        'original_height' => 0,

        'exif' => [],

        'path' => null,
        'dir' => null,
        'filename' => null,
        'basename' => null,

        'dimensions' => [],
    ];

    /**
     * The class constructor.
     *
     * @access public
     */
    public function __construct()
    {
        $this->prepareConfig();
    }

    /**
     * The main and the only one method exposed to public and used.
     *
     * @access public
     * @param  UploadedFile $uploadedFile
     * @param  string       $newfilename  (default: null)
     * @param  string       $dir          (default: null)
     * @return array
     */
    public function upload(UploadedFile $uploadedFile, $newfilename = null, $dir = null)
    {
        $isPathOk = $this->checkPathIsOk($this->uploadpath, $dir);
        $isImage = $this->checkIsImage($uploadedFile);

        if (! $isPathOk || ! $isImage) {
            return $this->results;
        }

        if (! $uploadedFile) {
            return $this->results;
        }

        $this->getUploadedFileProperties($uploadedFile);
        $this->setNewFilename($newfilename);

        if (! $this->saveFileToPath($uploadedFile)) {
            return $this->results;
        }

        $this->createThumbnails();

        return $this->results;
    }

    /**
     * Prepare and set configs.
     *
     * @access private
     */
    private function prepareConfig()
    {
        $this->library = config('imageupload.library', 'gd');
        $this->quality = config('imageupload.quality', 90);
        $this->uploadpath = config('imageupload.path', public_path('uploads/images'));
        $this->newfilename = config('imageupload.newfilename', 'original');
        $this->dimensions = config('imageupload.dimensions');
        $this->suffix = config('imageupload.suffix', true);
        $this->exif = config('imageupload.exif', false);

        return $this;
    }

    /**
     * Get image processing library (Intervention).
     * 
     * @access private
     * @return ImageManager
     */
    private function getImageLibrary()
    {
        $driver = $this->library;
        
        if (! in_array($this->library, ['gd', 'imagick'])) {
            $driver = 'gd';
        }
        
        return (new ImageManager(compact('driver')));
    }

    /**
     * Create the thumbnails for uploaded image.
     *
     * @access private
     */
    private function createThumbnails()
    {
        if (empty($this->dimensions) || ! is_array($this->dimensions)) {
            return $this;
        }
        
        $sourceFilePath = $this->results['original_filepath'];

        foreach ($this->dimensions as $name => $dimension) {
            if (empty($dimension) || ! is_array($dimension)) {
                continue;
            }

            list($width, $height, $crop) = $dimension;

            $height = (! empty($height) ? $height : $width);
            $crop = (isset($crop) ? $crop : false);

            $resized = $this->resizeImage($sourceFilePath, $name, $width, $height, $crop);
            
            if (empty($resized)) {
                continue;
            }

            $this->results['dimensions'][$name] = $resized;
        }

        return $this;
    }

    /**
     * Save (move) uploaded file to location and save as original file.
     *
     * @access private
     * @param UploadedFile $uploadedFile
     */
    private function saveFileToPath(UploadedFile $uploadedFile)
    {
        $uploaded = $uploadedFile->move($this->results['path'], $this->results['filename']);

        if (! $uploaded) {
            $this->results['error'] = 'File '.$this->results['original_filename '].' is not uploaded.';

            return false;
        }

        $uploadedFilePath = implode('/', [
            $this->results['path'], $this->results['filename'],
        ]);

        $this->results['original_filepath'] = $uploadedFilePath;
        $this->results['original_filedir'] = $this->getDirFromPath($uploadedFilePath);
        $this->results['basename'] = pathinfo($uploadedFilePath, PATHINFO_FILENAME);

        $this->getImageDimension($uploadedFilePath);

        return true;
    }

    /**
     * Set and get uploaded file dimension.
     *
     * @access private
     * @param string $uploadedFilePath
     */
    private function getImageDimension($uploadedFilePath)
    {
        list($width, $height) = getimagesize($uploadedFilePath);
        $this->results['original_width'] = $width;
        $this->results['original_height'] = $height;

        return $this;
    }

    /**
     * Get EXIF data from uploaded file.
     *
     * @access private
     * @param  string $uploadedFilepath
     * @param  mixed  $sourceFilePath
     * @return array
     */
    private function getExif($sourceFilePath)
    {
        $exifdata = [];

        if (! $this->exif) {
            return $exifdata;
        }

        try {
            $image = $this->imagine
                ->setMetadataReader(new ExifMetadataReader())
                ->open($sourceFilePath);
            $metadata = $image->metadata();
            $exifdata = $metadata->toArray();

            return $exifdata;
        } catch (Exception $e) {
            return $exifdata;
        }
    }

    /**
     * Check if path is exists and writeable. If not exists, create it.
     *
     * @access private
     * @param  string $path
     * @param  string $dir  (default: null)
     * @return bool
     */
    private function checkPathIsOk($path, $dir = null)
    {
        $path = implode('/', array_filter([
            rtrim($path, '/'), trim($dir, '/'),
        ]));

        $this->results['path'] = $path;
        $this->results['dir'] = $this->getDirFromPath($path);

        if (File::isDirectory($path) && File::isWritable($path)) {
            return true;
        }

        try {
            @File::makeDirectory($path, 0777, true);

            return true;
        } catch (Exception $e) {
            $this->results['error'] = $e->getMessage();

            return false;
        }
    }

    /**
     * Get relative path from absolute path.
     *
     * @access private
     * @param  string $path
     * @param  string $base (default: null)
     * @return string
     */
    private function getDirFromPath($path, $base = null)
    {
        if (empty($base)) {
            $base = public_path();
        }

        return trim(str_replace(public_path(), '', $path), '/');
    }

    /**
     * Check if uploaded file is Image.
     *
     * @access private
     * @param  UploadedFile $uploadedFile
     * @return bool
     */
    private function checkIsImage(UploadedFile $uploadedFile)
    {
        if (substr($uploadedFile->getMimeType(), 0, 5) == 'image') {
            return true;
        }

        return false;
    }

    /**
     * Set new file name based on config.
     *
     * @access private
     * @param string $newfilename (default: null)
     */
    private function setNewFilename($newfilename = null)
    {
        $extension = $this->results['original_extension'];
        $originalFilename = $this->results['original_filename'];

        switch ($this->newfilename) {
            case 'hash':
                $newfilename = md5($originalFilename.strtotime('now'));
                break;
            case 'random':
                $newfilename = Str::random();
                break;
            case 'timestamp':
                $newfilename = strtotime('now');
                break;
            case 'custom':
                $newfilename = (! empty($newfilename) ? $newfilename : $originalFilename);
                break;
            default:
                $newfilename = pathinfo($originalFilename, PATHINFO_FILENAME);
        }

        $this->results['filename'] = $newfilename.'.'.$extension;

        return $this;
    }

    /**
     * Set original uploaded file properties.
     *
     * @access private
     * @param UploadedFile $uploadedFile
     */
    private function getUploadedFileProperties(UploadedFile $uploadedFile)
    {
        $this->results['original_filename'] = $uploadedFile->getClientOriginalName();
        $this->results['original_filepath'] = $uploadedFile->getRealPath();
        $this->results['original_extension'] = $uploadedFile->getClientOriginalExtension();
        $this->results['original_filesize'] = $uploadedFile->getSize();
        $this->results['original_mime'] = $uploadedFile->getMimeType();
        $this->results['exif'] = $this->getExif($uploadedFile->getRealPath());

        return $this;
    }

    /**
     * Set path for resized images.
     *
     * @access private
     * @param  string $name
     * @return string
     */
    private function getResizedFilePath($name)
    {
        $name = trim(Str::slug($name), '/');

        $resizedPath = $this->results['path'];
        $resizedBasename = implode('_', [
            $this->results['basename'], $name,
        ]);

        if (! $this->suffix) {
            $resizedPath = implode('/', [
                $this->results['path'], $name,
            ]);
            $resizedBasename = $this->results['basename'];
        }

        $resizedBasename .= '.'.$this->results['original_extension'];

        return implode('/', [$resizedPath, $resizedBasename]);
    }

    /**
     * Resize file to create thumbnail.
     *
     * @access private
     * @param  string $sourceFilePath
     * @param  string $name
     * @param  int    $width
     * @param  int    $height         (default: null)
     * @param  bool   $crop           (default: false)
     * @return array
     */
    private function resizeImage($sourceFilePath, $name, $width, $height = null, $crop = false)
    {
        if (! $height) {
            $height = $width;
        }

        $resizedFilePath = $this->getResizedFilePath($name);

        try {
            $isPathOk = $this->checkPathIsOk(dirname($resizedFilePath));
            
            if (! $isPathOk) {
                return [];
            }
            
            $intervention = $this->getImageLibrary();
            $image = $intervention->make($sourceFilePath);
            
            if ($crop) {
                $width = ($height < $width ? $height : $width);
                $height = $width;
                
                $image->fit($width, $height, function($image) {
                    $image->upsize();
                });
            } else {
                $image->resize($width, $height, function($image) {
                    $image->aspectRatio();
                });
            }            
            
            $image->save($resizedFilePath, $this->quality);
            
            list($width, $height) = getimagesize($resizedFilePath);
            $filesize = filesize($resizedFilePath);

            return [
                'path' => dirname($resizedFilePath),
                'dir' => $this->getDirFromPath(dirname($resizedFilePath)),
                'filename' => pathinfo($resizedFilePath, PATHINFO_BASENAME),
                'filepath' => $resizedFilePath,
                'filedir' => $this->getDirFromPath($resizedFilePath),
                'width' => $width,
                'height' => $height,
                'filesize' => $filesize,
            ];
        } catch (Exception $e) {
            return [];
        }
    }
}
