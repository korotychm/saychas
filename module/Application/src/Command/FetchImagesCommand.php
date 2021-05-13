<?php

// src\Command\FetchImages.php

namespace Application\Command;

use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Laminas\Db\Adapter\Adapter;
use Ramsey\Uuid\Uuid;
use Application\Helper\FtpHelper;

class FetchImagesCommand extends Command
{

    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $name;

    /**
     * Constructor
     *
     * @param Adapter $adapter
     * @param type $name
     */
    public function __construct(Adapter $adapter, /* mixed */ $name, ContainerInterface $container)
    {
        parent::__construct($name);
        $this->adapter = $adapter;
        $this->name = $name;
        $this->container = $container;
    }

    /** @var string */
    protected static $defaultName = 'fetch-images';

    /**
     * Configures command

     * @return void

     */
    protected function configure(): void
    {
        $this->setName(self::$defaultName);
        $this->addOption('name', null, InputOption::VALUE_REQUIRED, 'Application');
    }

    /**
     * Executes the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $myuuid = Uuid::uuid4();
        $output->writeln('Fetch images: ' . $myuuid->toString() . ' ' . $this->name);
        $output->writeln("\n");
        
        $statement = $this->adapter->createStatement("select `image` from `provider`");
        $statement->prepare();
        $result = $statement->execute();
        
        $images = [];
        foreach($result as $r) {
            if(!empty($r['image'])) {
                $images[] = $r['image'];
            }
        }

        FtpHelper::fetch($this->container, 'brand', $images);

        return 0;
    }

}

//        $hydrator = new \Laminas\Hydrator\ClassMethodsHydrator();
//        $hydrator->addStrategy(
//                'posts',
//                new \Laminas\Hydrator\Strategy\CollectionStrategy(
//                        new \Laminas\Hydrator\ClassMethodsHydrator(),
//                        Post::class
//                )
//        );
//
//        $user = new User();;
//        $hydrator->hydrate(
//                [
//                    'firstName' => 'David Bowie',
//                    'lastName' => 'Let\'s Dance',
//                    'emailAddress' => 'asdf@banzaii.vonzaii',
//                    'phoneNumber' => '1234567890',
//                    'posts' => [],
//                ],
//                $user
//        );
//
//        $users = $this->userRepository->findAll([]);
//
//        $strategy = new \Laminas\Hydrator\Strategy\CollectionStrategy(
//                new \Laminas\Hydrator\ClassMethodsHydrator(),
//                \Application\Model\Entity\User::class
//        );
//
//        $users = $this->userRepository->findAll([]);
//        foreach ($users as $user) {
//            print_r($user->getFirstName() . ' ' . $user->getLastName() . ' ' . $user->getEmailAddress() . ' ' . $user->getPhoneNumber());
//            echo "\n";
//            foreach ($user->getPosts() as $post) {
//                echo $post->id . ' ' . $post->email . ' ' . $post->blog . "\n";
//            }
//        }

////class User extends Entity
//{
//
//    protected $firstName;
//    protected $lastName;
//    protected $emailAddress;
//    protected $phoneNumber;
//    protected $posts;
//
//    public function camelize($string)
//    {
//        $words = explode('_', $string);
//
//        // make a strings first character uppercase
//        $words = array_map('ucfirst', $words);
//
//        // join array elements with '-'
//        $string = implode('', $words);
//
//        return $string;
//    }
//
//    public function setFirstName(string $firstName)
//    {
//        $this->firstName = $firstName;
//        return $this;
//    }
//
//    public function getFirstName()
//    {
//        return $this->firstName;
//    }
//
//    public function setLastName(string $lastName)
//    {
//        $this->lastName = $lastName;
//        return $this;
//    }
//
//    public function getLastName()
//    {
//        return $this->lastName;
//    }
//
//    public function setEmailAddress(string $emailAddress)
//    {
//        $this->emailAddress = $emailAddress;
//        return $this;
//    }
//
//    public function getEmailAddress()
//    {
//        return $this->emailAddress;
//    }
//
//    public function setPhoneNumber(string $phoneNumber)
//    {
//        $this->phoneNumber = $phoneNumber;
//        return $this;
//    }
//
//    public function getPhoneNumber()
//    {
//        return $this->phoneNumber;
//    }
//
//    public function getPosts()
//    {
//        $hydrator = new \Laminas\Hydrator\ClassMethodsHydrator();
////        $hydrator->addStrategy(
////            'posts',
////            new \Laminas\Hydrator\Strategy\CollectionStrategy(
////                new \Laminas\Hydrator\ClassMethodsHydrator(),
////                Post::class
////            )
////        );
//        $strategy = new \Laminas\Hydrator\Strategy\CollectionStrategy(
//                new \Laminas\Hydrator\ClassMethodsHydrator(),
//                Post::class
//        );
//
//        $this->posts = $strategy->hydrate($this->posts);
//
//        return $this->posts;
//    }
//
//    public function setPosts($posts)
//    {
//        //$this->posts = [];//  $posts;
//        $this->posts = [
//            [
//                'id' => '1',
//                'email' => 'email@google.com',
//                'blog' => 'blog1',
//            ],
//            [
//                'id' => '2',
//                'email' => 'email1@google.com',
//                'blog' => 'blog2',
//            ],
//            [
//                'id' => '3',
//                'email' => 'email2@google.com',
//                'blog' => 'blog3',
//            ],
//        ];
//
//        return $this;
//    }
//
//}
//
//class Post extends Entity
//{
//
//    protected ?string $id;
//    protected ?string $email;
//    protected ?string $blog;
//
//    public function __construct(?string $id = null, ?string $email = null, ?string $blog = null)
//    {
//        $this->id = $id;
//        $this->email = $email;
//        $this->blog = $blog;
//    }
//
//    public function setId($id)
//    {
//        $this->id = $id;
//        return $this;
//    }
//
//    public function getId()
//    {
//        return $this->id;
//    }
//
//    public function setEmail($email)
//    {
//        $this->email = $email;
//        return $this;
//    }
//
//    public function getEmail()
//    {
//        return $this->email;
//    }
//
//    public function setBlog($blog)
//    {
//        $this->blog = $blog;
//        return $this;
//    }
//
//    public function getBlog()
//    {
//        return $this->blog;
//    }
//
//}
