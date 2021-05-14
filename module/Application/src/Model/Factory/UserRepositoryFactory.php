<?php

// src/Model/Factory/UserRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\User;
//use Application\Model\Entity\Entity;
use Application\Model\Repository\UserRepository;
//use Application\Model\Repository\PostRepository;
//use Laminas\Hydrator\Aggregate\HydrateEvent;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\EventManager\EventManagerInterface;
//use Laminas\EventManager\EventManager;
//use Application\Hydrator\UserHydrator;
// use Laminas\Hydrator\ReflectionHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class UserRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof UserRepository) {
            throw new Exception("not instanceof UserRepository");
        }

        $adapter = $container->get(AdapterInterface::class);
//        $postRepository = $container->get(PostRepository::class);
//        $cache             = new \Laminas\Cache\Storage\Adapter\Memory;// new Memory();
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
//            return $user;
//        };

        $hydrator = new \Laminas\Hydrator\Aggregate\AggregateHydrator();
        $hydrator->add(new \Laminas\Hydrator\ClassMethodsHydrator);
        $hydrator->add(new \Laminas\Hydrator\ReflectionHydrator);
//        $hydrator->add( new \Application\Hydrator\UserHydrator($postRepository));
//        $hydrator = new UserHydrator($postRepository);
//        $hydrator->getEventManager()->attach(HydrateEvent::EVENT_HYDRATE, $userListener, 1000);

        $prototype = new User;
//        $prototype::$postRepository = $postRepository;

        return new UserRepository(
                $adapter,
                $hydrator,
                $prototype
        );
    }

}
