<?php 

namespace DxlMembership\Classes\Repositories;

use Dxl\Classes\Abstracts\AbstractRepository as Repository;

use DxlMembership\Classes\Repositories\MemberProfileRepository as MemberProfile;

if( !class_exists('MemberRepository') )
{
    class MemberRepository extends Repository
    {
        protected $repository = "members";
        protected $defaultOrder = "DESC";
        protected $primaryIdentifier = "id";

        public function getAwaiting()
        {
            return $this->select()->where('is_payed', '0')->get();
        }

        public function latest($limit) 
        {
            return $this->select()->limit($limit)->get();
        }

        public function getProfile($profile)
        {
            return (new MemberProfile())->select()->where('member_id', $profile)->getRow();
        }
    }
}

?>