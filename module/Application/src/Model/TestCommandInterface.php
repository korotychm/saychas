<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Model;

interface TestCommandInterface
{
    /**
     * Persist a new test in the system.
     *
     * @param Test $test The test to insert; may or may not have an identifier.
     * @return Test The inserted test, with identifier.
     */
    public function insertTest(Test $test);

    /**
     * Update an existing test in the system.
     *
     * @param Test $test The test to update; must have an identifier.
     * @return Test The updated test.
     */
    public function updateTest(Test $test);

    /**
     * Delete a test from the system.
     *
     * @param Test $test The post to delete.
     * @return bool
     */
    public function deleteTest(Test $test);
}