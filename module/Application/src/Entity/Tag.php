<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Этот класс представляет собой тег.
 * @ORM\Entity
 * @ORM\Table(name="tag")
 */
class Tag 
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id")
     */
    protected $id;

    /** 
     * @ORM\Column(name="name") 
     */
    protected $name;

    // Возвращает ID данного тега.
    public function getId() 
    {
        return $this->id;
    }

    // Задает ID данного тега.
    public function setId($id) 
    {
        $this->id = $id;
    }

    // Возвращает имя.
    public function getName() 
    {
        return $this->name;
    }

    // Задает имя.
    public function setName($name) 
    {
        $this->name = $name;
    }
    
    /**
     * @ORM\ManyToMany(targetEntity="\Application\Entity\Post", mappedBy="tags")
     */
    protected $posts;
    
    // Конструктор.
    public function __construct() 
    {        
        $this->posts = new ArrayCollection();        
    }
  
    // Возвращает посты, связанные с данным тегом.
    public function getPosts() 
    {
        return $this->posts;
    }
    
    // Добавляет пост в коллекцию постов, связанных с этим тегом.
    public function addPost($post) 
    {
        $this->posts[] = $post;        
    }
    
}
