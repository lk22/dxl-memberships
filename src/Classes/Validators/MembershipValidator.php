<?php 
namespace DxlMembership\Classes\Validators;
require_once(ABSPATH . "wp-content/plugins/dxl-core/src/Classes/Validators/Validator.php");

use DXL\Core\Validators\Validator;

if( !class_exists('MembershipValidator') ) 
{
    class MembershipValidator extends Validator
    {
        /**
         * Define specific rules
         *
         * @var array
         */
        protected $rules = [
            "name" => [
                "rule" => "required",
                "validation" => ""
            ],
            "length" => [
                "rule" => "required",
                "validation" => ""
            ]
        ];

        /**
         * Member validator constructor
         *
         * @param array $request
         */
        public function __construct($request)
        {
            $this->request = $request;
        }

        /**
         * validating the request fields
         *
         * @return void
         * @throws Exception
         */
        public function validate()
        {
            return $this->request;
            foreach($this->rules as $key => $value)
            {

            }

            return true;
        }
    }
}

interface Loggable 
{
    public function log();
}


?>