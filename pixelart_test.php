<?php
/**
 * Lib:            Standalone Testing Script for Pixelate Library.
 * Description:    Test transformation functionality without WP.
 * Version:        1.0
 * Author:         Liki Crus
 */
require_once 'pixelart_lib.php';

$TEST_MODE = TRUE;
// Test case for the simple pixelate.
//create_pixel_art("test.jpg", "testing");


// Test case for the scaling pixelate.
$box_cnt = 10;
$box_size = 0.2;
create_fixed_pixel_art("emo.png", "emo_result", $box_cnt, $box_cnt);



