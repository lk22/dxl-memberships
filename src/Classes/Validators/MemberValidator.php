<?php 
namespace DxlMembership\Classes\Validators;

use DXL\Core\Validators\Validator;

if( !class_exists('MemberValidator') ) 
{
    class MemberValidator extends Validator
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
            foreach($this->rules as $key => $value)
            {
                // return $this->request;
                if( in_array($this->rules[$key], $this->request) ){
                    if($this->rules[$key]["rule"] == "required") {
                        if(empty($this->request[$key])) {
                            return ["validation" => "Feltet: " . $key . " er påkrævet"];
                        }
                    }
                }
            }

            return true;
        }
    }
}



?>