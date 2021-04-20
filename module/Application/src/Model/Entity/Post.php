<?php

/*
 * Here comes the text of your license
 * Each line should be prefixed with  * 
 */

namespace Application\Model\Entity;

/**
 * Description of Post
 *
 * @author alex
 */
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
