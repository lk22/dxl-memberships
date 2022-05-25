<?php 

require_once(dirname(__FILE__) . "/AbstracSanitizer.php");

if(!class_exists('MemberSanitizer'))
{
    class MemberSanitizer extends AbstractSanitizer
    {
        public function __construct() {}

        public function sanitize($data): array
        {
            $this->sanitizables = $data;
            $sanitized = [];
            foreach($this->sanitizables as $s => $value) {
                if( gettype($value) == "string") {

                    if( $s == "email" ) {
                        $sanitized[$s] = esc_html__(sanitize_email($value));
                    }

                    $sanitized[$s] = esc_html__(sanitize_text_field( $value ));
                }

                $sanitized[$s] = esc_html($value);
            }
            return $sanitized;
        }
    }
}
?>