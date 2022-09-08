<?php 

namespace DxlMembership\Classes\Repositories;

use Dxl\Classes\Abstracts\AbstractRepository as Repository;

use DxlMembership\Classes\Repositories\MemberProfileRepository as MemberProfile;

if ( !defined('ABSPATH') ) exit;
 
if( ! class_exists('MembershipActivityRepository') )
{
    class MembershipActivityRepository extends Repository
    {
        protected $repository = "memberships_activity";
        protected $defaultOrder = "DESC";
        protected $primaryIdentifier = "id";
    }
}
