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
    public function resizeImage($soureceFile, $destFile, $w, $h, $crop = FALSE, $type = "jpeg")
    {
        if (!$src = $this->openImage ($soureceFile)){
         return false ;   
        }
        
        list($width, $height) = getimagesize($soureceFile);
//       
//         $types = array(false, "gif", "jpeg", "png"); // Массив-шаблон  названий  типов изображений для getimagesize()
//        $ext = $types[$type]; // Название типа
//
//        if ($ext) {
//            $func = 'imagecreatefrom'.$ext; // Имя функции создания изображения для типа
//        } else {
//                return false;
//        }

        $r = $width / $height;

        if ($crop) {
            if ($width > $height) {
                $width = ceil($width - ($width * abs($r - $w / $h)));
            } else {
                $height = ceil($height - ($height * abs($r - $w / $h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w / $h > $r) {
                $newwidth = $h * $r;
                $newheight = $h;
            } else {
                $newheight = $w / $r;
                $newwidth = $w;
            }
        }

        
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        $func = 'image'.$type; // Имя функции для сохранения результата

        return $func($dst, $destFile); // Сохраняет изображение в  файл, возвращая результат этой операции true / false
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
                $img = @imagecreatefrompsd($src); //@
                break;
            default:
                $img = false;
                break;
        }
        return $img;
    }

}
