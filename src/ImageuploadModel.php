<?php

namespace Matriphe\Imageupload;

use Config;
use Illuminate\Database\Eloquent\Model;

class ImageuploadModel extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'original_filename' => 'string',
        'original_filepath' => 'string',
        'original_filedir' => 'string',
        'original_extension' => 'string',
        'original_mime' => 'string',
        'original_filesize' => 'integer',
        'original_width' => 'integer',
        'original_height' => 'integer',
        'path' => 'string',
        'dir' => 'string',
        'filename' => 'string',
        'basename' => 'string',
        'exif' => 'array',
    ];

    /**
     * The keys used in thumbnail.
     *
     * @var array
     */
    protected $thumbnailKeys = [
        'path' => 'string',
        'dir' => 'string',
        'filename' => 'string',
        'filepath' => 'string',
        'filedir' => 'string',
        'width' => 'integer',
        'height' => 'integer',
        'filesize' => 'integer',
        'is_squared' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'original_filename', 'original_filepath', 'original_filedir',
        'original_extension', 'original_mime', 'original_filesize',
        'original_width', 'original_height',
        'path', 'dir', 'filename', 'basename',
        'exif',
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $tableName = Config::get('imageupload.table', 'image_uploads');

        $this->setTable($tableName);
    }

    /**
     * Get dimension fillable field.
     *
     * @return array
     */
    public function getDimensionKeys()
    {
        $dimensions = Config::get('imageupload.dimensions');

        $fillable = [];

        if (empty($dimensions) || ! is_array($dimensions)) {
            return $fillable;
        }

        foreach ($dimensions as $name => $dimension) {
            foreach ($this->thumbnailKeys as $key => $cast) {
                array_push($fillable, $name.'_'.$key);
            }
        }

        return $fillable;
    }

    /**
     * Get the casts array.
     *
     * @return array
     */
    public function getCasts()
    {
        $this->casts = parent::getCasts();

        $dimensions = Config::get('imageupload.dimensions');

        if (empty($dimensions) || ! is_array($dimensions)) {
            return $this->casts;
        }

        foreach ($dimensions as $name => $dimension) {
            foreach ($this->thumbnailKeys as $key => $cast) {
                $this->casts[$name.'_'.$key] = $cast;
            }
        }

        return $this->casts;
    }

    /**
     * Get the fillable attributes for the model.
     *
     * @return array
     */
    public function getFillable()
    {
        return array_merge($this->fillable, $this->getDimensionKeys());
    }

    /**
     * Mutate Exif JSON to array on reading.
     *
     * @param  string $value
     * @return array
     */
    public function getExifAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * Mutate Exif array to JSON on writing.
     *
     * @param array $value
     */
    public function setExifAttribute($value)
    {
        $this->attributes['exif'] = json_encode($value);
    }
}
