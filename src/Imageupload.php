<?php

namespace Matriphe\Imageupload;

use Carbon\Carbon;
use Config;
use Exception;
use File;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Imageupload
{
    /**
     * The results array
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
    public function __construct(ImageManager $intervention)
    {
        $this->intervention = $intervention;

        $this->prepareConfigs();
    }

    /**
     * The main method, upload the file.
     *
     * @access public
     * @param  UploadedFile $uploadedFile
     * @param  string       $newFilename  (default: null)
     * @param  string       $path         (default: null)
     * @return array
     */
    public function upload(UploadedFile $uploadedFile, $newFilename = null, $path = null)
    {
        $this->prepareTargetUploadPath($path);

        $this->getUploadedOriginalFileProperties($uploadedFile);

        $this->setNewFilename($newFilename);

        $this->saveOriginalFile($uploadedFile);

        $this->createThumbnails($uploadedFile);

        return $this->returnOutput();
    }

    /**
     * Set output on the fly.
     *
     * @access public
     * @param string $string (default: null)
     */
    public function output($string = null)
    {
        if (in_array($string, ['collection', 'json', 'array', 'db'])) {
            $this->output = $string;
        }

        return $this;
    }

    /**
     * Get and prepare configs.
     *
     * @access private
     */
    private function prepareConfigs()
    {
        $this->library = Config::get('imageupload.library', 'gd');
        $this->quality = Config::get('imageupload.quality', 90);
        $this->uploadpath = Config::get('imageupload.path', public_path('uploads/images'));
        $this->newfilename = Config::get('imageupload.newfilename', 'original');
        $this->dimensions = Config::get('imageupload.dimensions');
        $this->suffix = Config::get('imageupload.suffix', true);
        $this->exif = Config::get('imageupload.exif', false);
        $this->output = Config::get('imageupload.output', 'array');

        $this->intervention->configure(['driver' => $this->library]);

        return $this;
    }

    /**
     * Check and create directory if not exists.
     *
     * @access private
     * @param  string $absoluteTargetPath
     * @return bool
     */
    private function createDirectoryIfNotExists($absoluteTargetPath)
    {
        if (File::isDirectory($absoluteTargetPath) && File::isWritable($absoluteTargetPath)) {
            return true;
        }

        try {
            @File::makeDirectory($absoluteTargetPath, 0777, true);

            return true;
        } catch (Exception $e) {
            throw new ImageuploadException($e->getMessage());
        }
    }

    /**
     * Set target upload path.
     *
     * @access private
     * @param  string $path (default: null)
     * @return bool
     */
    private function prepareTargetUploadPath($path = null)
    {
        $absoluteTargetPath = implode('/', array_filter([
            rtrim($this->uploadpath, '/'), trim(dirname($path), '/'),
        ]));

        $this->results['path'] = $absoluteTargetPath;
        $this->results['dir'] = $this->getRelativePath($absoluteTargetPath);

        return $this->createDirectoryIfNotExists($absoluteTargetPath);
    }

    /**
     * Set thumbnail target upload file path.
     *
     * @access private
     * @param  string $key
     * @return string
     */
    private function getThumbnailsTargetUploadFilepath($key)
    {
        $absoluteThumbnailTargetPath = implode('/', array_filter([
            rtrim($this->results['path'], '/'), (! $this->suffix ? trim($key) : ''),
        ]));

        $this->createDirectoryIfNotExists($absoluteThumbnailTargetPath);

        $resizedBasename = implode('_', [
            $this->results['basename'], $key,
        ]);

        if (! $this->suffix) {
            $resizedBasename = $this->results['basename'];
        }

        $resizedBasename .= '.'.$this->results['original_extension'];

        return implode('/', [$absoluteThumbnailTargetPath, $resizedBasename]);
    }

    /**
     * Get relative path from absolute path.
     *
     * @access private
     * @param  string $absoluteTargetPath
     * @return string
     */
    private function getRelativePath($absoluteTargetPath)
    {
        return trim(str_replace(public_path(), '', $absoluteTargetPath), '/');
    }

    /**
     * Set new file name from config.
     *
     * @access private
     * @param string $newfilename (default: null)
     */
    private function setNewFilename($newfilename = null)
    {
        $extension = $this->results['original_extension'];
        $originalFilename = $this->results['original_filename'];

        $timestamp = Carbon::now()->getTimestamp();

        switch ($this->newfilename) {
            case 'hash':
                $newfilename = md5($originalFilename.$timestamp);
                break;
            case 'random':
                $newfilename = Str::random(16);
                break;
            case 'timestamp':
                $newfilename = $timestamp;
                break;
            case 'custom':
                $newfilename = (! empty($newfilename) ? $newfilename : $originalFilename);
                break;
            default:
                $newfilename = pathinfo($originalFilename, PATHINFO_FILENAME);
        }

        $this->results['basename'] = (string) $newfilename;
        $this->results['filename'] = $newfilename.'.'.$extension;

        return $this;
    }

    /**
     * Upload and save original file.
     *
     * @access private
     * @param UploadedFile $uploadedFile
     */
    private function saveOriginalFile(UploadedFile $uploadedFile)
    {
        try {
            $targetFilepath = implode('/', [
                $this->results['path'], $this->results['filename'],
            ]);

            $image = $this->intervention->make($uploadedFile);

            if ($this->exif && ! empty($image->exif())) {
                $this->results['exif'] = $image->exif();
            }

            $image->save($targetFilepath, $this->quality);

            $this->results['original_width'] = (int) $image->width();
            $this->results['original_height'] = (int) $image->height();
            $this->results['original_filepath'] = $targetFilepath;
            $this->results['original_filedir'] = $this->getRelativePath($targetFilepath);
        } catch (Exception $e) {
            throw new ImageuploadException($e->getMessage());
        }

        return $this;
    }

    /**
     * Prepare original file properties.
     *
     * @access private
     * @param UploadedFile $uploadedFile
     */
    private function getUploadedOriginalFileProperties(UploadedFile $uploadedFile)
    {
        $this->results['original_filename'] = $uploadedFile->getClientOriginalName();
        $this->results['original_filepath'] = $this->getRelativePath($uploadedFile->getRealPath());
        $this->results['original_filedir'] = $uploadedFile->getRealPath();
        $this->results['original_extension'] = $uploadedFile->getClientOriginalExtension();
        $this->results['original_filesize'] = (int) $uploadedFile->getSize();
        $this->results['original_mime'] = $uploadedFile->getMimeType();

        return $this;
    }

    /**
     * Resize file to create thumbnail.
     *
     * @access private
     * @param  UploadedFile $uploadedFile
     * @param  string       $targetFilepath
     * @param  int          $width
     * @param  int          $height         (default: null)
     * @param  bool         $squared        (default: false)
     * @return array
     */
    private function resizeCropImage(UploadedFile $uploadedFile, $targetFilepath, $width, $height = null, $squared = false)
    {
        try {
            $height = (! empty($height) ? $height : $width);
            $squared = (isset($squared) ? $squared : false);

            $image = $this->intervention->make($uploadedFile);

            if ($squared) {
                $width = ($height < $width ? $height : $width);
                $height = $width;

                $image->fit($width, $height, function ($image) {
                    $image->upsize();
                });
            } else {
                $image->resize($width, $height, function ($image) {
                    $image->aspectRatio();
                });
            }

            $image->save($targetFilepath, $this->quality);

            return [
                'path' => dirname($targetFilepath),
                'dir' => $this->getRelativePath($targetFilepath),
                'filename' => pathinfo($targetFilepath, PATHINFO_BASENAME),
                'filepath' => $targetFilepath,
                'filedir' => $this->getRelativePath($targetFilepath),
                'width' => (int) $image->width(),
                'height' => (int) $image->height(),
                'filesize' => (int) $image->filesize(),
                'is_squared' => (bool) $squared,
            ];
        } catch (Exception $e) {
            throw new ImageuploadException($e->getMessage());
        }
    }

    /**
     * Create thumbnails.
     *
     * @access private
     * @param UploadedFile $uploadedFile
     */
    private function createThumbnails(UploadedFile $uploadedFile)
    {
        if (empty($this->dimensions)) {
            return $this;
        }

        foreach ($this->dimensions as $key => $dimension) {
            if (empty($dimension) || ! is_array($dimension)) {
                continue;
            }

            list($width, $height, $squared) = $dimension;

            $targetFilepath = $this->getThumbnailsTargetUploadFilepath($key);

            $image = $this->resizeCropImage($uploadedFile, $targetFilepath, $width, $height, $squared);

            if (! $image) {
                continue;
            }

            $this->results['dimensions'][$key] = $image;
        }

        return $this;
    }

    /**
     * Return output.
     *
     * @access private
     * @return mixed
     */
    private function returnOutput()
    {
        $collection = new Collection($this->results);

        switch ($this->output) {
            case 'db':
                return $this->saveToDatabase($collection);
                break;
            case 'collection':
                return $collection;
                break;
            case 'json':
                return $collection->toJson();
                break;
            case 'array':
            default:
                return $collection->toArray();
        }
    }

    /**
     * Save output to database and return Model collection.
     *
     * @access private
     * @param  Collection       $collection
     * @return ImageuploadModel
     */
    private function saveToDatabase(Collection $collection)
    {
        $model = new ImageuploadModel();
        $fillable = $model->getFillable();
        $input = $collection->only($fillable)->toArray();

        $dimensions = $collection['dimensions'];

        foreach ($dimensions as $key => $dimension) {
            foreach ($dimension as $k => $v) {
                $input[$key.'_'.$k] = $v;
            }
        }

        return $model->firstOrCreate($input);
    }
}
