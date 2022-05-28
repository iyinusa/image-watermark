<?php
$image_path = "";
$font_path = "GILSANUB.TTF";
$font_size = 15;       // in pixcels
//$water_mark_text_1 = "9";
$water_mark_text_2 = "Protected";

function watermark_image($oldimage_name, $new_image_name){
    global $image_path;
    list($owidth,$oheight) = getimagesize($oldimage_name);
    $width = $height = 300;    
    $im = imagecreatetruecolor($width, $height);
    $img_src = imagecreatefromjpeg($oldimage_name);
    imagecopyresampled($im, $img_src, 0, 0, 0, 0, $width, $height, $owidth, $oheight);
    $watermark = imagecreatefrompng($image_path);
    list($w_width, $w_height) = getimagesize($image_path);        
    $pos_x = $width - $w_width; 
    $pos_y = $height - $w_height;
    imagecopy($im, $watermark, $pos_x, $pos_y, 0, 0, $w_width, $w_height);
    imagejpeg($im, $new_image_name, 100);
    imagedestroy($im);
    unlink($oldimage_name);
    return true;
}


function watermark_text($oldimage_name, $new_image_name){
    global $font_path, $font_size, $water_mark_text_1, $water_mark_text_2;
    list($owidth,$oheight) = getimagesize($oldimage_name);
    $width = $height = 300;    
    $image = imagecreatetruecolor($width, $height);
    $image_src = imagecreatefromjpeg($oldimage_name);
    imagecopyresampled($image, $image_src, 0, 0, 0, 0, $width, $height, $owidth, $oheight);
   // $black = imagecolorallocate($image, 0, 0, 0);
    $blue = imagecolorallocate($image, 79, 166, 185);
   // imagettftext($image, $font_size, 0, 30, 190, $black, $font_path, $water_mark_text_1);
    imagettftext($image, $font_size, 0, 68, 190, $blue, $font_path, $water_mark_text_2);
    imagejpeg($image, $new_image_name, 100);
    imagedestroy($image);
    unlink($oldimage_name);
    return true;
}
$demo_image= "";
if(isset($_POST['createmark']) and $_POST['createmark'] == "Upload Image"){
    $path = "uploads/";
	$water_mark_text_2 = $_POST['wname'];
    $valid_formats = array("jpg", "bmp","jpeg");
	$name = $_FILES['imgfile']['name'];
	if(strlen($name))
{
   list($txt, $ext) = explode(".", $name);
   if(in_array($ext,$valid_formats)&& $_FILES['imgfile']['size'] <= 256*1024)
	{
    $upload_status = move_uploaded_file($_FILES['imgfile']['tmp_name'], $path.$_FILES['imgfile']['name']);
    if($upload_status){
        $new_name = $path.time().".jpg";
        if(watermark_text($path.$_FILES['imgfile']['name'], $new_name))
                $demo_image = $new_name;
                
    }
	}
	else
	$msg="File size Max 256 KB or Invalid file format supports .jpg and .bmp";
	}
}
?>
<html>
    <head>
        <title>
            Digital Image Watermark
        </title>
        
        <style type="text/css">
            body{ width:800px; margin: 15px auto; padding:0px; font-family: arial; background-color:#eee;}    
            fieldset{padding:15px; border:3px double #ccc;}
			fieldset legend{padding:10px 30px; text-transform:uppercase; border:3px double #ccc;}
			input{padding:10px 40px; font-size:20px;}
        </style>
        
    </head>
    <body>
	

    <h1 style="text-align:center; text-transform:uppercase;">Digital Image Watermark</h1>
        <form name="imageUpload" id="imageUpload" method="post" enctype="multipart/form-data" >
            <fieldset>
                <legend>Upload Image</legend>
                <input type="file" name="imgfile" id="imgfile"/><br /><br />
                <input type="text" name="wname" placeholder="Watermark Name" />
                <input type="submit" name="createmark" id="createmark" value="Upload Image" />
            </fieldset>   
            <?php
                if(!empty($demo_image))
                    echo '<br/><center><img src="'.$demo_image.'" /></center>';
				else
				    echo '<h3>'.$msg.'</h3>';
            ?>
        </form>
    
   
    </body>
</html>
