<?php
/**
 * Created by PhpStorm.
 * User: wojoinc
 * Date: 10/12/17
 * Time: 1:16 PM
 */
include('funcs.php');

if (isset($_REQUEST['image'])) $image = $_REQUEST['image'];
if (isset($_REQUEST['message'])) $message = $_REQUEST['message'];

if (isset($image) && isset($message)) {

    $image = end(explode("\\", $image));
    //echo "parsed path: " . $image;
    $bin_message = strToByteArr($message);
    $len = sizeof($bin_message);
    //echo $len . "\n";
    $src = '../images/' . $image;

    list($img_h, $img_w, $attr, $info) = getimagesize($src);
    ini_set("memory_limit", '128M');

    $img = imagecreatefrompng($src);
    if ($img == false) echo 'Could not load image';

    // Turn off alpha blending and set alpha flag
    /*imagealphablending($img, false);
    imagesavealpha($img, true);*/

    $x = $y = 0;
    //encode message length into the first 8 bits
    for ($i = 0x7; $i >= 0; $i--) {
        $index = imagecolorat($img, $x, $y);
        $colors = imagecolorsforindex($img, $index);
        //echo "$i => " . bitAt($len, $i);
        setBitAt($colors['blue'], 0, bitAt($len, $i));
        //$colors['blue'] += bitAt($len,$i);
        $new_color = imagecolorallocate($img, $colors['red'], $colors['green'], $colors['blue']);
        //echo bitAt($colors['blue'], 0);
        imagesetpixel($img, $x, $y, $new_color);

        //verify
        $index = imagecolorat($img, $x, $y);
        $colors = imagecolorsforindex($img, $index);
        //echo bitAt($colors['blue'], 0) . "\n";

        if ($x + 1 > $img_w) {
            $x = 0;
            $y++;
        } else {
            $x++;
        }

    }
    foreach ($bin_message as $char) {

        for ($i = 7; $i >= 0; $i--) {
            $index = imagecolorat($img, $x, $y);
            $colors = imagecolorsforindex($img, $index);
            //echo "$i => " . bitAt($char, $i);
            setBitAt($colors['blue'], 0, bitAt($char, $i));
            $new_color = imagecolorallocate($img, $colors['red'], $colors['green'], $colors['blue']);
            //echo bitAt($colors['blue'], 0);
            imagesetpixel($img, $x, $y, $new_color);

            //verify
            $index = imagecolorat($img, $x, $y);
            $colors = imagecolorsforindex($img, $index);
            //echo bitAt($colors['blue'], 0) . "\n\n";

            if ($x + 1 > $img_w) {
                $x = 0;
                $y++;
            } else {
                $x++;
            }
        }
    }
    if (imagepng($img, '../images/encoded.png', 0)) {
        echo "images/encoded.png";
    }
    imagedestroy($img);
}