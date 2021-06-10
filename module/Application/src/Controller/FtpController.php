<?php

// src/Controller/ReceivingController.php

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Application\Helper\FtpHelper;

class FtpController extends AbstractActionController
{

    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Retrieve an image from ftp(params: POST)
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $post = $this->getRequest()->getPost()->toArray();
        return (new ViewModel(['table' => $post['table'], 'fileName' => $post['fileName']]))->setTerminal(true);
    }

    /**
     * Get image (params: GET)
     *
     * @return $image
     */
    public function getImageAction()
    {
        $params = $this->params()->fromRoute();
        $im = FtpHelper::fetchOne($this->config, $params['table'], $params['fileName']); // '1350x.jpg'
        return $im;
    }

}
