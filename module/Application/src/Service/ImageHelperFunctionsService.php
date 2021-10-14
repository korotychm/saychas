<?php

// src\Service\Factory\ImageHelperFunctionsService.php

namespace Application\Service;

use Laminas\Config\Config;
//use Laminas\Json\Json;
//use Laminas\Session\Container;
use Application\Resource\Resource;

//use Application\Model\Entity\ProductFavorites;
//use Application\Model\Entity\Basket;
//use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;

/**
 * Description of ImageHelperFunctionsService
 *
 */
class ImageHelperFunctionsService
{

    /**
     * @var Config
     */
    private $config;

    public function __construct($config)
    //,HandbookRelatedProductRepositoryInterface $productRepository)
    {
        $this->config = $config;
        //$this->productRepository = $productRepository;
    }

    /**
     * resize and save image file
     *
     * @param string $soureceFile
     * @param string $destFile
     * @param int $w
     * @param int $h
     * @param bool $crop
     * @param string $type
     * @return bool
     */
    public function resizeImage($soureceFile, $destFile, $w, $h, $type = "jpeg")
    {
        if (!$src = $this->openImage($soureceFile)) {
            return false;
        }

        list($width, $height) = getimagesize($soureceFile);
        $r = $width / $height;
        if ($w / $h > $r) {
            $newwidth = $h * $r;
            $newheight = $h;
        } else {
            $newheight = $w / $r;
            $newwidth = $w;
        }
        
        $im = $this->initImage($newwidth, $newheight);
        imagecopyresampled($im, $src, 0, 0, 0, 0,  $newwidth, $newheight, $width, $height);
        $func = 'image' . $type; 
        
        return $func($im, "$destFile.$type"); 
    }
    
    /**
     * resize, crop and save image file
     * 
     * @param string $soureceFile
     * @param string $destFile
     * @param int $width
     * @param int $height
     * @param string $type
     * @return boolean
     */
    public function cropImage($soureceFile, $destFile, $width, $height, $type = "jpeg")
    {
        if (!$src = $this->openImage($soureceFile)) {
            return false;
        }

        list($w, $h) = getimagesize($soureceFile);
        $x = $y = 0;
        $x_ratio = $width / $w;
        $y_ratio = $height / $h;
        $ratio = min($x_ratio, $y_ratio);
        $use_x_ratio = ($x_ratio == $ratio);
        $new_width = $use_x_ratio ? $width : floor($w * $ratio);
        $new_height = !$use_x_ratio ? $height : floor($h * $ratio);

        if ($use_x_ratio) {
            $k = $new_height / $height;
            $x = floor(($w - $w * $k) / 2);
            $w = floor($w - ($w - $w * $k));
        } else {
            $k = $new_width / $width;
            $y = floor(($h - $h * $k) / 2);
            $h = floor($h - ($h - $h * $k));
        }

        $im = $this->initImage($width, $height);
        imagecopyresampled($im, $src, 0, 0, $x, $y, $width, $height, $w, $h);
        $func = 'image' . $type; 

        return $func($im, "$destFile.$type"); 
    }

    /**
     * 
     * @param int $width
     * @param int $height
     * @return Image
     */
    private function initImage($width, $height)
    {
        $im = imageCreateTrueColor($width, $height);
        $color = imagecolorat($im, 1, 1);
        imagecolortransparent($im, $color);
        $bg = imagecolorallocatealpha($im, 255, 255, 255, 127);
        imagefill($im, 0, 0, $bg);
        imageAlphaBlending($im, false);
        imageSaveAlpha($im, true);

        return $im;
    }

    /**
     * check valid post image files
     *
     * @param array $files
     * @return boolean
     */
    public function getValidPostImage($files)
    {
        foreach ($files as $file) {
            if (!in_array($file['type'], Resource::LEGAL_IMAGE_TYPES)) {
                return false;
            }
        }
        return true;
    }

    /**
     *
     * @param string $src
     * @return image
     */
    private function openImage($src)
    {
        switch (exif_imagetype($src)) {
            case IMAGETYPE_PNG:
                $img = @imagecreatefrompng($src);
                break;
            case IMAGETYPE_GIF:
                $img = @imagecreatefromgif($src);
                break;
            case IMAGETYPE_JPEG:
                $img = @imagecreatefromjpeg($src);
                break;
            case IMAGETYPE_BMP:
                $img = @ImageCreateFromBMP($src);
                break;
            case IMAGETYPE_PSD:
                $img = @imagecreatefrompsd($src); 
                break;
            default:
                $img = false;
                break;
        }
        return $img;
    }

}
