<?php 

if( !class_exists('AbstractSanitizer') ) 
{
    abstract class AbstractSanitizer 
    {
        public $sanitizables = [];

        public abstract function sanitize($data): array;
    }
}



?>