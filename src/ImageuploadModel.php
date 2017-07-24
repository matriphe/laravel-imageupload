<?php

namespace Matriphe\Imageupload;

use Config;
use Illuminate\Database\Eloquent\Model;

class ImageuploadModel extends Model
{
    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        $tableName = Config::get('imageupload.table', 'image_uploads');
        
        $this->setTable($tableName);
    }

    /**
     * The keys used in thumbnail.
     *
     * @var array
     */
    protected $thumbnailKeys = [
        'path', 'dir', 'filename', 'filepath', 'filedir', 'width', 'height',
        'filesize',
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
            foreach ($this->thumbnailKeys as $key) {
                array_push($fillable, $name.'_'.$key);
            }
        }

        return $fillable;
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
