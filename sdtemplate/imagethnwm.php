<?php

	$path = $_SERVER['DOCUMENT_ROOT']."/user_images/898/thumbs/";
    $wmlogosmall = $_SERVER["DOCUMENT_ROOT"].'/'.'images/wmlogosmall.gif';
	$dir = opendir($path);

	$logo = imageCreateFromGif($wmlogosmall);
    $logoInfo = getImageSize($wmlogosmall);
    $logoWidth = $logoInfo[0];
    $logoHeight = $logoInfo[1];

    while(false !== ($file = readdir($dir)))
    {
    	//$thnFile = $file; //$path.$file;
        $file = $path.$file;
        echo "<hr>$file<hr>";
        $info = getimagesize($file);
        $thnMimeType = $info['mime'];
        $width = $info[0];
        $height = $info[1];
        echo "W: $width H: $height MT: $thnMimeType<br>";

        if($thnMimeType == 'image/gif')
        {
            $thn = imageCreateFromGif($file);
        }
        elseif($thnMimeType == 'image/png')
        {
           $thn = imageCreateFromPng($file);
        }
        elseif($thnMimeType == 'image/jpg' || $thnMimeType == 'image/jpeg')
        {
            $thn = imageCreateFromJpeg($file);
        }

        //$fl = fopen($thnFile, "w");
        //fclose($fl);
        echo "Logo: $logo<br>Img: $thn<br>";

        imageCopyMerge($thn, $logo, $width-$logoWidth-5, $height-$logoHeight-5, 0, 0, $logoWidth, $logoHeight, 70);

        if($thnMimeType == 'image/gif')
        {
            imageGif($thn, $file);
        }
        if($thnMimeType == 'image/png')
        {
            imagePng($thn, $file);
        }
        if($thnMimeType == 'image/jpeg' || $thnMimeType == 'image/jpg')
        {
           imageJpeg($thn, $file);
        }
    }
?>