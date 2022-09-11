<?php 

    namespace DxlMembership\Classes\Views;

    use Dxl\Interfaces\ViewInterface;

    if ( ! defined('ABSPATH') ) exit;

    if ( ! class_exists('MembershipDetailsView') ) 
    {
        class MembershipDetailsView implements ViewInterface 
        {
            public function render() {}
        }
    }

?>