<?php namespace Matriphe\Imageupload;

use Config, File, Log, Str;

class Imageupload {

  protected $imagine;
  protected $library;

  public $results = [];

  public function __construct()
  {
    if (!$this->imagine)
    {
      $this->library = Config::get('imageupload.library','gd');
      $this->quality = Config::get('imageupload.quality', 90);
      $this->uploadpath = Config::get('imageupload.path', public_path().'/uploads/images');
      $this->newfilename = Config::get('imageupload.newfilename', 'original');
      $this->dimensions = Config::get('imageupload.dimensions');
      $this->suffix = Config::get('imageupload.suffix',true);

      // Now create the instance
      if     ($this->library == 'imagick') $this->imagine = new \Imagine\Imagick\Imagine();
      elseif ($this->library == 'gmagick') $this->imagine = new \Imagine\Gmagick\Imagine();
      elseif ($this->library == 'gd')      $this->imagine = new \Imagine\Gd\Imagine();
      else                                 $this->imagine = new \Imagine\Gd\Imagine();
    }
  }

  private function checkPathIsOk($path,$dir=null)
  {
    $path = rtrim($path,'/') . ($dir ? '/'.trim($dir,'/') : '');

    if (File::isDirectory($path) && File::isWritable($path))
    {
      return true;
    }
    else
    {
      try
      {
        @File::makeDirectory($path, 0777, true);
        return true;
      }
      catch (\Exception $e)
      {
        Log::error('Imageupload: ' . $e->getMessage());
        $this->results['error'] = $e->getMessage();
        return false;
      }
    }
  }

  public function upload($filesource, $newfilename=null, $dir=null)
  {
    $isPathOk = $this->checkPathIsOk($this->uploadpath,$dir);

    if ($isPathOk)
    {
      if ($filesource)
      {
        $this->results['path'] = rtrim($this->uploadpath,'/') . ($dir ? '/'.trim($dir,'/') : '');
        $this->results['dir'] = str_replace(public_path().'/', '', $this->results['path']);
        $this->results['original_filename'] = $filesource->getClientOriginalName();
        $this->results['original_filepath'] = $filesource->getRealPath();
        $this->results['original_extension'] = $filesource->getClientOriginalExtension();
        $this->results['original_filesize'] = $filesource->getSize();
        $this->results['original_mime'] = $filesource->getMimeType();
        
        switch ($this->newfilename)
        {
          case 'hash':
            $this->results['filename'] = md5($this->results['original_filename'].'.'.$this->results['original_extension'].strtotime('now')).'.'.$this->results['original_extension'];
            break;
          case 'random':
            $this->results['filename'] = Str::random().'.'.$this->results['original_extension'];
          break;
          case 'timestamp':
            $this->results['filename'] = strtotime('now').'.'.$this->results['original_extension'];
            break;
          case 'custom':
            $this->results['filename'] = (!empty($newfilename) ? $newfilename.'.'.$this->results['original_extension'] : $this->results['original_filename'].'.'.$this->results['original_extension']);
            break;
          default:
            $this->results['filename']= $this->results['original_filename'];
        }

        $uploaded = $filesource->move($this->results['path'], $this->results['filename']);
        if ($uploaded)
        {
          $this->results['original_filepath'] = rtrim($this->results['path']).'/'.$this->results['filename'];
          $this->results['original_filedir'] = str_replace(public_path().'/', '', $this->results['original_filepath']);
          $this->results['basename'] = pathinfo($this->results['original_filepath'],PATHINFO_FILENAME);

          list($width, $height) = getimagesize($this->results['original_filepath']);
          $this->results['original_width'] = $width;
          $this->results['original_height'] = $height;

          $this->createDimensions($this->results['original_filepath']);
        }
        else
        {
          $this->results['error'] = 'File ' . $this->results['original_filename '].' is not uploaded.';
          Log::error('Imageupload: ' . $this->results['error']);
        }
      }
    }

    return $this->results;
  }

  protected function createDimensions($filesource)
  {
    if (!empty($this->dimensions) && is_array($this->dimensions))
    {
      foreach ($this->dimensions as $name => $dimension)
      {
        $width   = (int) $dimension[0];
        $height  = isset($dimension[1]) ?  (int) $dimension[1] : $width;
        $crop    = isset($dimension[2]) ? (bool) $dimension[2] : false;

        $this->resize($filesource, $name, $width, $height, $crop);
      }
    }
  }

  private function resize($filesource, $suffix, $width, $height, $crop)
  {
    if (!$height) $height = $width;

    $suffix = trim($suffix);

    $path = $this->results['path'] . ($this->suffix == false ? '/'.trim($suffix,'/') : '');
    $name = $this->results['basename'] . ($this->suffix == true ? '_'.trim($suffix,'/') : '') . '.' . $this->results['original_extension'];

    $pathname = $path . '/' . $name;

    //print_r($width.' '.$height.' '.$crop);

    try
    {
      $isPathOk = $this->checkPathIsOk($this->results['path'],($this->suffix == false ? $suffix : ''));

      if ($isPathOk)
      {
        $size = new \Imagine\Image\Box($width, $height);
        $mode = $crop ? \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND : \Imagine\Image\ImageInterface::THUMBNAIL_INSET;
        $newfile = $this->imagine->open($filesource)->thumbnail($size, $mode)->save($pathname,['quality'=>$this->quality]);

        list($nwidth, $nheight) = getimagesize($pathname);
        $filesize = filesize($pathname);

        $this->results['dimensions'][$suffix] = [
          'path' => $path,
          'dir' => str_replace(public_path().'/', '', $path),
          'filename' => $name,
          'filepath' => $pathname,
          'filedir' => str_replace(public_path().'/', '', $pathname),
          'width' => $nwidth,
          'height' => $nheight,
          'filesize' => $filesize,
        ];
      }
    }
    catch (\Exception $e)
    {

    }
  }
}