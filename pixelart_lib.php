<?php

/**
 * Lib:            Pixelate Library.
 * Description:    Transform it into pixel art.
 * Version:        1.0
 * Author:         Liki Crus
 */

/*
* image - the location of the image to pixelate 
* output - the name of the output file (NOTE: not extension should be provided.)
* box_cnt - the count of boxes on X axis (default 10)
* box_size - the size of box (default 0.5cm)
* 
*/
if (!function_exists('create_fixed_pixel_art')) {

    function create_fixed_pixel_art($image, $output, $box_cnt = 20, $box_size = 0.5)
    {
        global $TEST_MODE;
        static $CN_UNIT = 37.7952755906;

        try {
            // check if the input file exists
            if (!file_exists($image)) {
                echo 'File "' . $image . '" not found';
                return NULL;
            }

            // now we have the image loaded up and ready for the effect to be applied
            // get the image size
            list($src_width, $src_height) = getimagesize($image);

            // Calculate the new image size
            $pixelate_x = ceil($box_size * $CN_UNIT);
            $pixelate_y = ceil($box_size * $CN_UNIT);

            // Get the result image size.
            $width = $box_cnt * $pixelate_x;
            $height = $width;
            $src_img = NULL;

            $img = imagecreatetruecolor($width, $height);

            // get the input file extension and create a GD resource from it
            $ext = exif_imagetype($image);

            if ($ext == IMAGETYPE_JPEG) {
                $src_img = imagecreatefromjpeg($image);
            } elseif ($ext == IMAGETYPE_PNG) {
                $src_img = imagecreatefrompng($image);

                imagesavealpha($img, true);

            } elseif ($ext == IMAGETYPE_GIF) {
                $src_img = imagecreatefromgif($image);
            } else {
                echo 'Unsupported file extension';
                return NULL;
            }
            $transparent = imagecolorallocatealpha($img, 255, 255, 255, 127);
            imagefill($img, 0, 0, $transparent);

            // Resizing img with dest width and height.
            $dest_width = $width;
            $dest_height = $height;

            if ($src_width > $src_height) {
                // width_based.
                $dest_height = floor($src_height * $width / $src_width);
            } else {
                $dest_width = floor($src_width * $height / $src_height);
            }

            $top = floor(abs($height - $dest_height) / 2);
            $left = floor(abs($width - $dest_width) / 2);
            imagecopyresampled($img, $src_img, $left, $top, 0, 0, $dest_width, $dest_height, $src_width, $src_height);
            imagedestroy($src_img);

            // Image copy : We will use $img as a src image.
            // $img --> $src_img
            $src_img = imagecreatetruecolor($width, $height);
            imagesavealpha($src_img, true);
            $transparent = imagecolorallocatealpha($src_img, 0, 0, 0, 127);
            imagefill($src_img, 0, 0, $transparent);
            imagecopy($src_img, $img, 0, 0, 0, 0, $width, $height);

            // if PNG, we need to set the transparency.
            if ($ext == IMAGETYPE_PNG) {
                $img = imagecreatetruecolor($width, $height);
                imagesavealpha($img, true);

                $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
                imagefill($img, 0, 0, $transparent);
            }

            // start from the top-left pixel and keep looping until we have the desired effect
            for ($y = 0; $y < $height; $y += $pixelate_y) {
                for ($x = 0; $x < $width; $x += $pixelate_x) {
                    $color = get_closet_color_from_region($src_img, $x, $y, $pixelate_x, $pixelate_y);
                    $color = imagecolorresolvealpha($img, $color['red'], $color['green'], $color['blue'], $color['alpha']);

                    imagefilledrectangle($img, $x, $y, $x + $pixelate_x, $y + $pixelate_y, $color);
                }
            }

            // Drawing grid
            $color_ground = imagecolorallocate($img, 100, 100, 100);
            for ($y = 0; $y < $height; $y += $pixelate_y) {
                imageline($img, 0, $y, $width, $y, $color_ground);
            }

            for ($x = 0; $x < $width; $x += $pixelate_x) {
                imageline($img, $x, 0, $x, $height, $color_ground);
            }

            imageline($img, 0, $height - 1, $width - 1, $height - 1, $color_ground);
            imageline($img, $width - 1, 0, $width - 1, $height - 1, $color_ground);

            // save the image
            $ext_name = image_type_to_extension($ext);
            $output = $output . $ext_name;

            if ($ext == IMAGETYPE_PNG) {
                imagealphablending($img, true);
                imagesavealpha($img, true);

                imagepng($img, $output);
            } else if ($ext == IMAGETYPE_JPEG) {
                imagejpeg($img, $output);
            } elseif ($ext == IMAGETYPE_GIF) {
                imagegif($img, $output);
            }

            imagedestroy($img);
            imagedestroy($src_img);

            return basename($output);

        } catch (Exception $e) {
            echo $e;
        }
    }
}

if (!function_exists('get_closet_color_from_region')) {
    function get_closet_color_from_region(&$img, $x, $y, $width, $height)
    {
        $rgb = imagecolorsforindex($img, imagecolorat($img, $x + 1, $y + 1));
        return $rgb;
    }
}