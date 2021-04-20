<?php

// src/Model/Repository/CharacteristicRepository.php

namespace Application\Hydrator;

use Laminas\Hydrator\HydratorInterface;
use Application\Model\Entity\User;
use Application\Model\Repository\PostRepository;
use Laminas\Hydrator\ClassMethodsHydrator;

/**
 * Description of UserHydrator
 *
 * @author alex
 */
class UserHydrator extends ClassMethodsHydrator implements HydratorInterface {

    /** @var PostRepository */
    private PostRepository $postRepository;

    /**
     * Constructor
     *
     * @param PostRepository $postRepository
     */
    public function __construct(PostRepository $postRepository) {
        $this->postRepository = $postRepository;
    }

    /**
     * Converts $data array into User $object
     *
     * @param array $data
     * @param User $object
     * @return User
     */
    public function hydrate($data, $object) {
        parent::hydrate($data, $object);
        
        if (!$object instanceof User) {
            return $object;
        }

        $object->setPosts($this->postRepository);

        return $object;
    }

    /**
     * Extracts $data from User $object
     *
     * @param object $object
     * @return array
     */
    public function extract(object $object): array {
        if (!$object instanceof User) {
            return array();
        }

        return [];
    }

}
