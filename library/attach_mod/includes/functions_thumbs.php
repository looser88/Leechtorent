<?php
/**
 * TorrentPier – Bull-powered BitTorrent tracker engine
 *
 * @copyright Copyright (c) 2005-2023 TorrentPier (https://torrentpier.com)
 * @link      https://github.com/torrentpier/torrentpier for the canonical source repository
 * @license   https://github.com/torrentpier/torrentpier/blob/master/LICENSE MIT License
 */

if (!defined('BB_ROOT')) {
    die(basename(__FILE__));
}

$imagick = '';

/**
 * Calculate the needed size for Thumbnail
 */
function get_img_size_format($width, $height)
{
    // Maximum Width the Image can take
    $max_width = 400;

    if ($width > $height) {
        return array(
            round($width * ($max_width / $width)),
            round($height * ($max_width / $width))
        );
    }

    return array(
        round($width * ($max_width / $height)),
        round($height * ($max_width / $height))
    );
}

/**
 * Check if imagick is present
 */
function is_imagick()
{
    global $imagick, $attach_config;

    if ($attach_config['img_imagick'] != '') {
        $imagick = $attach_config['img_imagick'];
        return true;
    }

    return false;
}

/**
 * Get supported image types
 */
function get_supported_image_types($type)
{
    if (@extension_loaded('gd')) {
        $format = imagetypes();
        $new_type = 0;

        switch ($type) {
            case 1:
                $new_type = ($format & IMG_GIF) ? IMG_GIF : 0;
                break;
            case 2:
            case 9:
            case 10:
            case 11:
            case 12:
                $new_type = ($format & IMG_JPG) ? IMG_JPG : 0;
                break;
            case 3:
                $new_type = ($format & IMG_PNG) ? IMG_PNG : 0;
                break;
            case 6:
            case 15:
                $new_type = ($format & IMG_WBMP) ? IMG_WBMP : 0;
                break;
        }

        return array(
            'gd' => ($new_type) ? true : false,
            'format' => $new_type,
            'version' => (function_exists('imagecreatetruecolor')) ? 2 : 1
        );
    }

    return array('gd' => false);
}

/**
 * Create thumbnail
 */
function create_thumbnail($source, $new_file, $mimetype)
{
    global $attach_config, $imagick;

    $source = amod_realpath($source);
    $min_filesize = (int)$attach_config['img_min_thumb_filesize'];
    $img_filesize = (@file_exists($source)) ? @filesize($source) : false;

    if (!$img_filesize || $img_filesize <= $min_filesize) {
        return false;
    }

    [$width, $height, $type,] = getimagesize($source);

    if (!$width || !$height) {
        return false;
    }

    [$new_width, $new_height] = get_img_size_format($width, $height);

    $used_imagick = false;

    if (is_imagick()) {
        passthru($imagick . ' -quality 85 -antialias -sample ' . $new_width . 'x' . $new_height . ' "' . str_replace('\\', '/', $source) . '" +profile "*" "' . str_replace('\\', '/', $new_file) . '"');
        if (@file_exists($new_file)) {
            $used_imagick = true;
        }
    }

    if (!$used_imagick) {
        $type = get_supported_image_types($type);

        if ($type['gd']) {
            switch ($type['format']) {
                case IMG_GIF:
                    $image = imagecreatefromgif($source);
                    break;
                case IMG_JPG:
                    $image = imagecreatefromjpeg($source);
                    break;
                case IMG_PNG:
                    $image = imagecreatefrompng($source);
                    break;
                case IMG_WBMP:
                    $image = imagecreatefromwbmp($source);
                    break;
            }

            if ($type['version'] == 1 || !$attach_config['use_gd2']) {
                $new_image = imagecreate($new_width, $new_height);
                imagecopyresized($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            } else {
                $new_image = imagecreatetruecolor($new_width, $new_height);
                imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            }

            switch ($type['format']) {
                case IMG_GIF:
                    imagegif($new_image, $new_file);
                    break;
                case IMG_JPG:
                    imagejpeg($new_image, $new_file, 90);
                    break;
                case IMG_PNG:
                    imagepng($new_image, $new_file);
                    break;
                case IMG_WBMP:
                    imagewbmp($new_image, $new_file);
                    break;
            }

            imagedestroy($new_image);
        }
    }

    if (!@file_exists($new_file)) {
        return false;
    }

    @chmod($new_file, 0664);

    return true;
}
