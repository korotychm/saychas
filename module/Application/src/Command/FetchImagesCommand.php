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
use Application\Model\Entity\Entity;
use laminas\Stdlib\Hydrator\Aggregate\ExtractEvent;
use Laminas\Hydrator\Filter\MethodMatchFilter;
use Laminas\Hydrator\Filter\FilterComposite;
use Laminas\Hydrator\Aggregate\HydrateEvent;

class User extends Entity
{
    protected $firstName;
    protected $lastName;
    protected $emailAddress;
    protected $phoneNumber;
    protected $posts;
    
    public function camelize($string)
    {
        $words = explode('_', $string);

        // make a strings first character uppercase
        $words = array_map('ucfirst', $words);

        // join array elements with '-'
        $string = implode('', $words);
        
        return $string;
    }

    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }
    
    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setEmailAddress(string $emailAddress)
    {
        $this->emailAddress = $emailAddress;
        return $this;
    }

    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    public function setPhoneNumber(string $phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }
    
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }
    
    public function getPosts()
    {
        $hydrator = new \Laminas\Hydrator\ClassMethodsHydrator();
//        $hydrator->addStrategy(
//            'posts',
//            new \Laminas\Hydrator\Strategy\CollectionStrategy(
//                new \Laminas\Hydrator\ClassMethodsHydrator(),
//                Post::class
//            )
//        );
        $strategy = new \Laminas\Hydrator\Strategy\CollectionStrategy(
            new \Laminas\Hydrator\ClassMethodsHydrator(),
            Post::class
        );

        $this->posts = $strategy->hydrate($this->posts);
        
        return $this->posts;
    }

    public function setPosts($posts)
    {
        //$this->posts = [];//  $posts;
        $this->posts = [
                    [
                        'id' => '1',
                        'email'    => 'email@google.com',
                        'blog' => 'blog1',
                    ],
                    [
                        'id' => '2',
                        'email'    => 'email1@google.com',
                        'blog' => 'blog2',
                    ],
                    [
                        'id' => '3',
                        'email'    => 'email2@google.com',
                        'blog' => 'blog3',
                    ],
            ];

        return $this;
    }
}

class Post extends Entity
{
    protected ?string $id;
    protected ?string $email;
    protected ?string $blog;
    
    public function __construct(?string $id = null, ?string $email = null, ?string $blog = null)
    {
        $this->id = $id;
        $this->email = $email;
        $this->blog = $blog;
    }
    
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }
    
    public function getEmail()
    {
        return $this->email;
    }
    
    public function setBlog($blog)
    {
        $this->blog = $blog;
        return $this;
    }
    
    public function getBlog()
    {
        return $this->blog;
    }
}

class FetchImagesCommand extends Command
{    
    /**
     * @var Adapter
     */
    private $adapter;
    
    /**
     * @var string
     */
    private $name;
    
    private $userRepository;
    
    private $postRepository;
    
    /**
     * Constructor
     * 
     * @param Adapter $adapter
     * @param type $name
     */
    public function __construct(Adapter $adapter, /*mixed */$name, $userRepository, $postRepository)
    {
        parent::__construct($name);
        $this->adapter = $adapter;
        $this->name = $name;
        $this->userRepository = $userRepository;
        $this->postRepository = $postRepository;
    }
    
    /** @var string */
    protected static $defaultName = 'fetch-images';

    /**
     * Configures command        $user->setFirstName('banzaii')->setLastName('vonzaii');

     * @return void        $user->setFirstName('banzaii')->setLastName('vonzaii');

     */
    protected function configure() : void
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
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $myuuid = Uuid::uuid4();
        //printf("Your UUID is: %s", $myuuid->toString());

        //$output->writeln('Fetch images: ' . $myuuid->toString() . $input->getOption('name'));
        $output->writeln('Fetch images: ' . $myuuid->toString() . ' '. $this->name);
        $output->writeln("\n");
        
//        $data = [
//            'first_name'    => 'James',
//            'last_name'     => 'Kahn',
//            'email_address' => 'james.kahn@example.org',
//            'phone_number'  => '+61 419 1234 5678',
//        ];

//        $hydrator = new \Laminas\Hydrator\ClassMethodsHydrator;// ObjectPropertyHydrator();
//        $refHydrator = new \Laminas\Hydrator\ReflectionHydrator();
//        
//        $user = $hydrator->hydrate($data, new User());
//        $data     = $hydrator->extract($user);
//        //$data     = $refHydrator->extract($user);
//        print_r($data);
//        echo "\n";
        
        $hydrator = new \Laminas\Hydrator\ClassMethodsHydrator();
        $hydrator->addStrategy(
            'posts',
            new \Laminas\Hydrator\Strategy\CollectionStrategy(
                new \Laminas\Hydrator\ClassMethodsHydrator(),
                Post::class
            )
        );
        
        $user = new User();
        $hydrator->hydrate(
            [
                'firstName' => 'David Bowie',
                'lastName'  => 'Let\'s Dance',
                'emailAddress' => 'asdf@banzaii.vonzaii',
                'phoneNumber' => '1234567890',
                'posts' => [],
//                'posts' => [
//                    [
//                        'id' => '111',
//                        'email'    => 'email@google.com',
//                        'blog' => 'blog1',
//                    ],
//                    [
//                        'id' => '222',
//                        'email'    => 'email1@google.com',
//                        'blog' => 'blog2', 
//                    ],
//                    [
//                        'id' => '333',
//                        'email'    => 'email2@google.com',
//                        'blog' => 'blog3',
//                    ],
//                ]
                    // …
            ],
            $user
        );
        
//        echo "\n\n\n";
//        echo $user->camelize('banzaiiVonzaii')."\n";
//        echo $user->firstName . ' ' . $user->getFirstName() . ' '. $user->getLastName() . ' ' . $user->getEmailAddress() . ' ' . $user->getPhoneNumber() . "\n\n\n";
//        foreach ($user->getPosts() as $post) {
//            echo $post->id . ' ' . $post->email . ' ' .$post->getBlog();
//            echo "\n";
//        }
//        
//        echo "\n\n\n\n\n";
        
        $users = $this->userRepository->findAll([]);
        
        $strategy = new \Laminas\Hydrator\Strategy\CollectionStrategy(
            new \Laminas\Hydrator\ClassMethodsHydrator(),
            \Application\Model\Entity\User::class
        );
        
//        $hydrator->addFilter(
//          'postrepository',
//          new MethodMatchFilter('getPostRepository'),
//          FilterComposite::CONDITION_AND
//        );

        
        
        $postRepository = $this->postRepository;
        
//        $userListener = function (HydrateEvent $event) use ($postRepository) {
//            $data = $event->getHydrationData();// 
//            
//            $strategy = new \Laminas\Hydrator\Strategy\CollectionStrategy(
//                new \Laminas\Hydrator\ClassMethodsHydrator(),
//                \Application\Model\Entity\Post::class
//            );
//            
//            
//            $hydrator = new \Laminas\Hydrator\ClassMethodsHydrator();
//            $user = $hydrator->hydrate($data, new User);
//            if( ! $user instanceof User) {
//                return;
//            }
//            $posts = $postRepository->findAll(['where'=>['id'=>$user->getPhoneNumber()]])->toArray();
//            $hydratedPosts = $strategy->hydrate($posts);
//            $user->setPosts($hydratedPosts);
//            print_r($user);
//            return 0;
//        };
//        
//        $h = new \Laminas\Hydrator\Aggregate\AggregateHydrator();
//        $h->add(new \Laminas\Hydrator\ClassMethodsHydrator());
//
//        //$hydrator = new \Laminas\Hydrator\ClassMethodsHydrator();
//        $h->getEventManager()->attach(HydrateEvent::EVENT_HYDRATE, $userListener, 1000);
//
//        foreach($users as $user) {
//            $u = $h->hydrate($user, new \Application\Model\Entity\User());
//            print_r($user);
//        }
        //$us = $h->hydrate($users->toArray());
//        
//        print_r($u);
        
        echo "=========================\n";
        $us = $strategy->hydrate($users->toArray());
//        print_r($us);
        echo "=========================\n";
        
        foreach($us as $u) {
            echo $u->getFirstName()."\n";
            foreach($u->getPosts() as $p) {
                print_r($p);
                //echo $p->getId().' '.$p->getEmail().' '.$p->getBlog()."\n";
            }
        }


        return 0;
    }
}