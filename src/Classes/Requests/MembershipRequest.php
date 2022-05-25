<?php 
    namespace DxlMembership\Classes\Requests;

    use Dxl\Classes\Abstracts\AbstractRequest as Request;
    use DxlMembership\Classes\Validators\MembershipValidator;

    if( !class_exists('MembershipRequest') ) {
        class MembershipRequest extends Request {

            /**
             * Validator
             *
             * @var DxlMembership\Classes\Validators\MembershipValidator $validator
             */
            protected $validator;

            /**
             * Module name
             *
             * @var string
             */
            protected $module = "membership";
            
            /**
             * Request class constructor
             */
            public function __construct()
            {
                parent::__construct();
            }

            /**
             * validate shortcut
             *
             * @return void
             */
            public function validate()
            {
                return $this->validator->validate();
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