<?php
namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Этот класс представляет собой комментарий, относящийся к посту блога.
 * @ORM\Entity
 * @ORM\Table(name="comment")
 */
class Comment 
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /** 
     * @ORM\Column(name="content")  
     */
    protected $content;

    /** 
     * @ORM\Column(name="author")  
     */
    protected $author;
    
    /** 
     * @ORM\Column(name="date_created")  
     */
    protected $dateCreated;

    // Возвращает ID данного комментария.
    public function getId() 
    {
        return $this->id;
    }

    // Задает ID данного комментария.
    public function setId($id) 
    {
        $this->id = $id;
    } 
    
    // Возвращает текст комментария.
    public function getContent() 
    {
        return $this->content;
    }

    // Устанавливает статус.
    public function setContent($content) 
    {
        $this->content = $content;
    }
    
    // Возвращает имя автора.
    public function getAuthor() 
    {
        return $this->author;
    }

    // Задает имя автора.
    public function setAuthor($author) 
    {
        $this->author = $author;
    }

    // Возвращает дату создания этого комментария.
    public function getDateCreated() 
    {
        return $this->dateCreated;
    }
    
    // Задает дату создания этого комментария.
    public function setDateCreated($dateCreated) 
    {
        $this->dateCreated = $dateCreated;
    }
    
    /**
     * @ORM\ManyToOne(targetEntity="\Application\Entity\Post", inversedBy="comments")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    protected $post;
     
    /*
     * Возвращает связанный пост.
     * @return \Application\Entity\Post
     */
    public function getPost() 
    {
        return $this->post;
    }
    
    /**
     * Задает связанный пост.
     * @param \Application\Entity\Post $post
     */
    public function setPost($post) 
    {
        $this->post = $post;
        $post->addComment($this);
    }
    
    
}
