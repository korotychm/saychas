<?php

// Application\src\Service\Factory\ImageHelperFunctionsServiceFactory.php

namespace Application\Service\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Service\ImageHelperFunctionsService;

//use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;

class ImageHelperFunctionsServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof ImageHelperFunctionsService) {
            throw new Exception("not instanceof ImageHelperFunctionsService");
        }

        $config = $container->get('Config');
        // $productRepository = $container->get(HandbookRelatedProductRepositoryInterface::class);

        return new ImageHelperFunctionsService($config/* , $productRepository */);
    }

    /**
     * resize and save image file
     * 
     * @param string $soureceFile //исходный файл
     * @param string $imageFile  //конечный файл
     * @param int $w   
     * @param int $h
     * @param bool $crop  
     * @return bool
     */
    public function resizeImage($soureceFile, $destFile, $w, $h, $crop = FALSE)
    {
        list($width, $height, $type) = getimagesize($soureceFile);
        $types = array(false, false, "jpeg", "png"); // Массив с типами изображений для getimagesize($file) 
        $ext = $types[$type]; // Зная "числовой" тип изображения, узнаём название типа
        
        if ($ext) {
            $func = 'imagecreatefrom'.$ext; // Получаем название функции  для создания изображения для типа
        } else {
                return false;
        }
        
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
        
        $src = $func($soureceFile);
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        $func = 'image'.$ext; // Имя функции для сохранения результата
        
        return $func($dst, $destFile); // Сохраняем изображение в  файл, возвращая результат этой операции

    }

}
