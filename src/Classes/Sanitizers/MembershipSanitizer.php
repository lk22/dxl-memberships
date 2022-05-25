<?php 

require_once(__DIR__ . "/AbstractSanitizer.php");

if(!class_exists('MembershipSanitizer'))
{
    class MembershipSanitizer extends AbstractSanitizer
    {
        protected $sanitizables;

        public function __construct() {}

        public function sanitize($data): array
        {
            $this->sanitizables = $data;
            $sanitized = [];

            return $sanitized;
        }
    }
}
?>