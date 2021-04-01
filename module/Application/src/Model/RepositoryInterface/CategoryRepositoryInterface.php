<?php
namespace Application\Model\RepositoryInterface;

interface CategoryRepositoryInterface
{
    /**
     * Return a set of all application tests that we can iterate over.
     *
     * Each entry should be a Test instance.
     *
     * @return string
     */
    
    public function findAllCategories($echo='', $i=0, $idActive);
    
    /**
     * Return массив всех родительских категорий   id=>title 
     *
     * Each entry should be a Test instance.
     *
     * @return array  
     */
   
    public function findAllMatherCategories ($i=0, $echo=[]);

   /**
     * Return массив всех id дочерних категорий 
     *
     * Each entry should be a Test instance.
     *
     * @return array  
     */
    
     public function findTreeCategories($i=0, $echo=[]);
     /**
     * Return a single application test.
     *
     * @param  int $id Identifier of the test to return.
     * @return Category
     */
   
    public function findCategory($id);
    
    /**
     * Return void
     * @param Entity
     */
    public function replace($entity);

    /**
     * Return void
     * @param json
     */
    public function delete($json);
}