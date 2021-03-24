<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Model;

class TestRepository implements TestRepositoryInterface
{
    private $data = [
        1 => [
            'id'    => 1,
            'name' => 'Hello World #1',
            //'text'  => 'This is our first blog post!',
        ],
        2 => [
            'id'    => 2,
            'name' => 'Hello World #2',
            //'text'  => 'This is our second blog post!',
        ],
        3 => [
            'id'    => 3,
            'name' => 'Hello World #3',
            //'text'  => 'This is our third blog post!',
        ],
        4 => [
            'id'    => 4,
            'name' => 'Hello World #4',
            //'text'  => 'This is our fourth blog post!',
        ],
        5 => [
            'id'    => 5,
            'name' => 'Hello World #5',
            //'text'  => 'This is our fifth blog post!',
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function findAllTests()
    {
        // TODO: Implement findAllTests() method.
        return array_map(function ($test) {
            return new Test(
                $test['name'],
                $test['id']
            );
        }, $this->data);
    }

    /**
     * {@inheritDoc}
     */
    public function findTest($id)
    {
        // TODO: Implement findTest() method.
        if (! isset($this->data[$id])) {
            throw new DomainException(sprintf('Post by id "%s" not found', $id));
        }

        return new Post(
            $this->data[$id]['name'],
            $this->data[$id]['id']
        );
    }
}