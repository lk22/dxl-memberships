<?php 
namespace DxlMembership\Classes\Cron;

use DxlMembership\Classes\Repositories\MemberRepository;

if( !defined('ABSPATH') ) {
    exit;
}

if( !class_exists('LookExpiredMemberships') ) {
    class LookExpiredMemberships {

        /**
         * Member repository
         * 
         * @var \DxlMembership\Classes\Repositories\MemberRepository
         */

        public function __construct()
        {
            $this->memberRepository = new MemberRepository();
            if( $_GET['action'] == 'dxl_look_expired_memberships' ) {
                $this->lookExpiredMemberships();
            }
        }
        
        private function look_expired_memberships()
        {
            $members = $this->memberRepository()->all();
            $fullYearExpiration = date('Y-m-d', strtotime('+1 year'));
            $halvYearExpiration = date('Y-m-d', strtotime('+6 months'));
            var_dump([$fullYearExpiration, $halvYearExpiration]);
        }
    }
}
?>