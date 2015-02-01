<?php

return array(

  'library' => 'gd',
  'quality' => 90,
  'path' => public_path().'/uploads/testing',
  'newfilename' => 'original',
  'dimensions' => array(

     'square50' => array(50, 50, true),
     'square100' => array(100, 100, true),
     'square200' => array(200, 200, true),
     'square400' => array(400, 400, true),

     'size50' => array(50, 50, false),
     'size100' => array(100, 100, false),
     'size200' => array(200, 200, false),
     'size400' => array(400, 400, false),
   ),
   'suffix' => true,
);