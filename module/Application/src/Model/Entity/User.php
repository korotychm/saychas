<?php

/*
 * Here comes the text of your license
 * Each line should be prefixed with  * 
 */

namespace Application\Model\Entity;

use Application\Model\Repository\PostRepository;
/**
 * Description of User
 *
 * @author alex
 */
class User extends Entity
{
    protected $postRepository;
    protected $firstName;
    protected $lastName;
    protected $emailAddress;
    protected $phoneNumber;
    protected $posts = [];
    
    public function __construct(PostRepository $postRepository = null)
    {
        $this->postRepository = $postRepository;

        $posts = $this->postRepository->findAll([]); //  'where'=>['id' => [$this->getPhoneNumber()] ]
        
        $strategy = new \Laminas\Hydrator\Strategy\CollectionStrategy(
            new \Laminas\Hydrator\ClassMethodsHydrator(),
            Post::class
        );

        $this->posts = $strategy->hydrate($posts->toArray());
        
        $this->setPosts($this->posts);
    }
    
    public function getPostRepository()
    {
        return $this->postRepository;
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
        //echo $this->phoneNumber."\n";
        //
//        echo "======================\n";
//        print_r($this->postRepository);
        
        //$this->getPostRepository()->findAll([]);
        
//        $hydrator = new \Laminas\Hydrator\ClassMethodsHydrator();
////        $hydrator->addStrategy(
////            'posts',
////            new \Laminas\Hydrator\Strategy\CollectionStrategy(
////                new \Laminas\Hydrator\ClassMethodsHydrator(),
////                Post::class
////            )
////        );
//        $strategy = new \Laminas\Hydrator\Strategy\CollectionStrategy(
//            new \Laminas\Hydrator\ClassMethodsHydrator(),
//            Post::class
//        );
//
//        $this->posts = $strategy->hydrate($this->posts);
        
        return $this->posts;
    }
    
    public function setPosts($posts)
    {
        $this->posts = $posts;
//        print_r($this->posts);
//        exit;
        //$this->posts = $this->postRepository->findAll([]);
//        $this->posts = [
//                    [
//                        'id' => '1',
//                        'email'    => 'email@google.com',
//                        'blog' => 'blog1',
//                    ],
//                    [
//                        'id' => '2',
//                        'email'    => 'email1@google.com',
//                        'blog' => 'blog2',
//                    ],
//                    [
//                        'id' => '3',
//                        'email'    => 'email2@google.com',
//                        'blog' => 'blog3',
//                    ],
//            ];

        return $this;
    }
    
}