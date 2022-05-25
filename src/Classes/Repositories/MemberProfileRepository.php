<?php 

namespace DxlMembership\Classes\Repositories;

use Dxl\Classes\Abstracts\AbstractRepository as Repository;

if( !class_exists('MemberProfileRepository') )
{
    class MemberProfileRepository extends Repository
    {
        protected $repository = "member_profile_settings";
        protected $defaultOrder = "DESC";
        protected $primaryIdentifier = "id";
    }
}

?>