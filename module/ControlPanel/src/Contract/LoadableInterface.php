<?php

// src/Contract/LoadableInterface.php

namespace ControlPanel\Contract;

interface LoadableInterface
{
    /**
     * Set number of rows per page
     * 
     * @param int $pageSize
     */
    function setPageSize(int $pageSize);

    /**
     * Calculate limits of given page
     * 
     * @param int $pageNumber
     * @return array
     */
    function calcLimits(int $pageNumber) : array;
    
    /**
     * Load array of data from 1C and store it into db
     * 
     * @param string $url
     * @param array $credentials
     * @return array
     */
    function loadAll(string $url, array $credentials = []) : array;
}
