<?php

//<img src="file.php?image=123.jpg[?maxX=200&maxY=150]"> (in [] = optional)

# standard height & weight if not given
if(!isset($maxX)) $maxX = 100;
if(!isset($maxY)) $maxY = 100;

# colour- & textvalues
$picBG = "0,0,0"; # RGB-value !
$picFG = "104,104,104"; # RGB-value !
$copyright = "";
$font = 2;

# minimal & maximum zoom
$minZoom = 1; # per cent related on orginal (!=0)
$maxZoom = 200; # per cent related on orginal (!=0)

# paths
$imgpath = $_GET['img_path']; # ending with "/" !
$nopicurl = "../thumbs/nopic.jpg"; # starting in $imagepath!!!
$nofileurl = "../thumbs/nofile.jpg"; # starting in $imagepath!!!

if(!isset($image) || empty($image))
   $imageurl = $imgpath . $nopicurl;
elseif(! file_exists($imgpath . trim($image)))
   $imageurl = $imgpath . $nofileurl;
else
   $imageurl = $imgpath . trim($image);

# reading image
$image = getImageSize($imageurl, $info); # $info, only to handle problems with earlier php versions...
switch($image[2]) {
     case 1:
       # GIF image
       $timg = imageCreateFromGIF($imageurl);
       break;
   case 2:
       # JPEG image
       $timg = imageCreateFromJPEG($imageurl);
       break;
   case 3:
       # PNG image
       $timg = imageCreateFromPNG($imageurl);
       break;
}

# reading image sizes
$imgX = $image[0];
$imgY = $image[1];

# calculation zoom factor
$_X = $imgX/$maxX * 100;
$_Y = $imgY/$maxY * 100;

# selecting correct zoom factor, so that the image always keeps in the given format
# no matter if it is more higher than wider or the other way around
if((100-$_X) < (100-$_Y)) $_K = $_X;
else $_K = $_Y;

# zoom check to the original
if($_K > 10000/$minZoom) $_K = 10000/$minZoom;
if($_K < 10000/$maxZoom) $_K = 10000/$maxZoom;

# calculate new image sizes
$newX = $imgX/$_K * 100;
$newY = $imgY/$_K * 100;

# set start positoin of the image
# always centered
$posX = ($maxX-$newX) / 2;
$posY = ($maxY-$newY) / 2;

# creating new image with given sizes
$imgh = imageCreateTrueColor($maxX, $maxY);

# setting colours
$cols = explode(",", $picBG);
$bgcol = imageColorallocate($imgh, trim($cols[0]), trim($cols[1]), trim($cols[2]));
$cols = explode(",", $picFG);
$fgcol = imageColorallocate($imgh, trim($cols[0]), trim($cols[1]), trim($cols[2]));

# fill background
imageFill($imgh, 0, 0, $bgcol);

# create small copy of the image
imageCopyResampled($imgh, $timg, $posX, $posY, 0, 0, $newX, $newY, $image[0], $image[1]);

# writing copyright note
imageStringUp($imgh, $font, $maxX-9, $maxY-3, $copyright, $fgcol);

# output
switch($image[2]) {
     case 1:
   # GIF image
       header("Content-type: image/gif");
       imageGIF($imgh);
   case 2:
   # JPEG image
       header("Content-type: image/jpeg");
       imageJPEG($imgh);
   case 3:
   # PNG image
       header("Content-type: image/png");
       imagePNG($imgh);
}

# cleaning cache
imageDestroy($timg);
imageDestroy($imgh);

?>