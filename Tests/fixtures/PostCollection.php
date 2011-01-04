<?php

require_once __DIR__.'/../../DMM/BaseDomainModel.php';

class PostCollection extends \DMM\ModelCollection
{
    public function __construct()
    {
        parent::__construct('\Post');
    }
}
