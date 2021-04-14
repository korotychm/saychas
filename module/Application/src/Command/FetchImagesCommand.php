<?php
// src\Command\FetchImages.php
/*
 * Here comes the text of your license
 * Each line should be prefixed with  * 
 */
//namespace Application\Command;
//
//use Symfony\Component\Console\Command\Command;
//use Symfony\Component\Console\Input\InputInterface;
//use Symfony\Component\Console\Output\OutputInterface;
//use Laminas\Db\Adapter\AdapterInterface;
///**
// * Description of FetchImages
// *
// * @author alex
// */
//class FetchImagesCommand extends Command
//{
//    //put your code here
//    private $adapter;
//    public function __construct(AdapterInterface $adapter, mixed $name = null)
//    {
//        parent::__construct($name);
//        $this->adapter = $adapter;
//    }
//    
//    protected function execute(InputInterface $input, OutputInterface $output)
//    {
//        return 1;
//    }
//}

namespace Application\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Laminas\Db\Adapter\Adapter;
use Ramsey\Uuid\Uuid;

class FetchImagesCommand extends Command
{
    
    private $adapter;
    private $name;
    
    public function __construct(Adapter $adapter, /*mixed */$name = null)
    {
        parent::__construct($name);
        $this->adapter = $adapter;
        $this->name = $name;
    }
    
    /** @var string */
    protected static $defaultName = 'fetch-images';

    /**
     * Configures command
     * @return void
     */
    protected function configure() : void
    {
        $this->setName(self::$defaultName);
        $this->addOption('name', null, InputOption::VALUE_REQUIRED, 'Application');
    }

    /**
     * Executes the command
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $myuuid = Uuid::uuid4();
        //printf("Your UUID is: %s", $myuuid->toString());

        //$output->writeln('Fetch images: ' . $myuuid->toString() . $input->getOption('name'));
        $output->writeln('Fetch images: ' . $myuuid->toString() . ' '. $this->name);

        return 0;
    }
}