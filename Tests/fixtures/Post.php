<?php

require_once __DIR__.'/../../DMM/BaseDomainModel.php';

class Post extends \DMM\BaseDomainModel
{
    public function __construct()
    {
        parent::__construct('post_id');
    }
}
