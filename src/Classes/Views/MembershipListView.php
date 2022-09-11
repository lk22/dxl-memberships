<?php 

    namespace DxlMembership\Classes\Views;

    use Dxl\Interfaces\ViewInterface;

    if ( ! defined('ABSPATH') ) exit;

    if ( ! class_exists('MembershipListView') ) 
    {
        class MembershipListView implements ViewInterface 
        {
            public function render() {}
        }
    }

?>