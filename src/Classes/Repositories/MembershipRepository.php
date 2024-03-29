<?php 

namespace DxlMembership\Classes\Repositories;

use Dxl\Classes\Abstracts\AbstractRepository as Repository;

if ( !defined('ABSPATH') ) exit;
 
if( !class_exists('MembershipRepository') )
{
    class MembershipRepository extends Repository
    {
        protected $repository = "memberships";
        protected $defaultOrder = "DESC";
        protected $primaryIdentifier = "id";
    }
}

?>