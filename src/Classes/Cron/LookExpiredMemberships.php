<?php 
namespace DxlMembership\Classes\Cron;

use DxlMembership\Classes\Repositories\MemberRepository;
use DxlMembership\Classes\Repositories\MembershipRepository;

use DxlMembership\Classes\Mails\MembershipCanceled;

use DXL\Classes\Core;

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
        protected $memberRepository;

        /**
         * MembershipRepository
         *
         * @var \DxlMembership\Classes\Repositories\MembershipRepository
         */
        protected $membershipRepository;

        /**
         * Constructor
         */
        public function __construct()
        {
            $this->memberRepository = new MemberRepository();
            if( $_GET['action'] == 'dxl_look_expired_memberships' ) {
                $this->look_expired_memberships();
            }
        }
        
        /**
         * Look up expired memberships and deactivated them
         *
         * @return void
         */
        private function look_expired_memberships()
        {
            $logger = new Core;
            $logger->getUtility('Logger');

            $members = $this->memberRepository
                ->select([
                    "id", 
                    "name", 
                    "email", 
                    "membership", 
                    "approved_date"
                ])->get();
            $currentDate = date('d-m-Y');
            $fullYearExpiration = date('d-m-Y', strtotime('first day of next year'));
            $halvYearExpiration = date('d-m-Y', strtotime('last day of june'));
            var_dump(["members" => $members]);
            die();

            foreach ($members as $member) {

                // validate if member is auto renewing
                if( ! $member->auto_renew ) {
                    $membership = $this->membershipRepository->find($member->membership);
                    $sendCanceledMail = new MembershipCanceled($member);

                    // if full year membership is expired
                    if (
                        $member->membership == 7 && 
                        $currentDate > $fullYearExpiration &&
                        date('d-m-Y', $member->approved_date) > $fullYearExpiration
                    ) {
                        $this->memberRepository->update($member->id, [
                            'is_payed' => 0,
                            'is_pending' => 1
                        ]);
                        
                        $logger->log("Member {$member->id} is now deactivated due to remaining payment, with folloing membership: {$membership->name}", 'memberships');
                        
                        $sendCanceledMail
                            ->setSubjet("Annulleret medlemskab")
                            ->setReceiver($member->email)
                            ->send();
                    }

                    // if half year membership is expired
                    if (
                        $member->membership == 6 && 
                        $currentDate > $halvYearExpiration &&
                        date('d-m-Y', $member->approved_date) > $halvYearExpiration
                    ) {
                        $this->memberRepository->update($member->id, [
                            'is_payed' => 0,
                            'is_pending' => 1
                        ]);
                        
                        $logger->log("Member {$member->id} is now deactivated due to remaining payment, with folloing membership: {$membership->name}", 'memberships');
                        
                        $sendCanceledMail
                            ->setSubjet("Annulleret medlemskab")
                            ->setReceiver($member->email)
                            ->send();
                    }
                }  

            }
        }
    }
}
?>