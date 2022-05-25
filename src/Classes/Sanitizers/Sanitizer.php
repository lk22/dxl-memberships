<?php 

    require_once(__DIR__ . "/MemberSanitizer.php");

    if( !class_exists('Sanitizer') )
    {
        class Sanitizer {
            public function __construct($sanitizer)
            {
                $this->sanitizer = $this->getSanitizer($sanitizer);
            }

            public function getSanitizer($sanitizer)
            {
                switch($sanitizer) {
                    case 'MemberSanitizer':
                        return new MemberSanitizer();
                        break;
                }
            }
        }
    }

?>