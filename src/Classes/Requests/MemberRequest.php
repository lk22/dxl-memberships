<?php 
    namespace DxlMembership\Classes\Requests;

    use Dxl\Classes\Abstracts\AbstractRequest as Request;

    if( !class_exists('MemberRequest') ) {
        class MemberRequest extends Request {
            protected $module = "member";
            public function __construct()
            {
                parent::__construct();
            }

            /**
             * get value from request field
             *
             * @param string $field
             * @return void
             */
            public function get($field = '')
            {
                return ($this->has($field)) ? $this->request[$this->module][$field] : false;
            }

            /**
             * check existence in request object
             *
             * @param string $field
             * @return boolean
             */
            public function has($field): bool 
            {
                return (isset($this->request[$this->module][$field])) ? true : false;
            }
        }
    }
?>