<?php 
namespace DxlMembership\Classes\Cron;

use DxlMembership\Classes\Repositories\MemberRepository;
use DxlMembership\Classes\Repositories\MembershipRepository;

use DxlMembership\Classes\Mails\MembershipCanceled;

use DxlEvents\Classes\Repositories\LanRepository;
use DxlEvents\Classes\Repositories\LanParticipantRepository;

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
         * LanRepository
         *
         * @var \DxlEvents\Classes\Repositories\LanRepository
         */
        protected $lanRepository;

        /**
         * Undocumented variable
         *
         * @var \DxlEvents\Classes\Repositories\LanParticipantRepository
         */
        protected $lanParticipantRepository;

        /**
         * Constructor
         */
        public function __construct()
        {
            $this->memberRepository = new MemberRepository();
            $this->membershipRepository = new MembershipRepository();
            $this->lanParticipantRepository = new LanParticipantRepository();
            $this->lanRepository = new LanRepository();
            if( isset($_GET['task']) && $_GET["task"] == 'dxl_look_expired_memberships' ) {
                $this->lookup_expired_memberships();
            }
        }
        
        /**
         * Look up expired memberships and deactivated them
         *
         * @return void
         */
        private function lookup_expired_memberships()
        {
            
            $logger = (new Core())->getUtility('Logger');
            
            $logger->log('Running LookExpiredMemberships cron job, looking for expired memberships hold on...', 4);

            $members = $this->memberRepository
                ->select([
                    "id", 
                    "name", 
                    "email",
                    "gamertag", 
                    "membership", 
                    "approved_date",
                    "auto_renew"
                ])->where('auto_renew', 0)->get();

            $currentDate = date('d-m-Y');
            $fullYearExpiration = date('d-m-Y', strtotime('first day of next year'));
            $halvYearExpiration = date('d-m-Y', strtotime('last day of june'));

            // find the latest lan event
            $event = $this->lanRepository->select()->descending('id')->limit(1)->get();

            foreach ($members as $member) {

                // find the member participant
                $lanParticipant = $this->lanParticipantRepository->select()->where('member_id', $member->id)->whereAnd('event_id', $event->id)->get();
                var_dump($lanParticipant);
                // if full year membership is expired
                if (
                    $member->membership == 7 && 
                    $currentDate == $fullYearExpiration &&
                    date('d-m-Y', $member->approved_date) > $fullYearExpiration
                ) {
                    $this->memberRepository->update([
                        'is_payed' => 0,
                        'is_pending' => 1
                    ], $member->id);
                    
                    $logger->log("Member {$member->id}: {$member->gamertag} is now deactivated due to remaining payment, with folloing membership: 6 måneder", 4);
                    
                    $sendCanceledMail = (new MembershipCanceled($member))
                        ->setSubject("Annulleret medlemskab")
                        ->setReciever($member->email)
                        ->send();
                }

                // if half year membership is expired
                if (
                    $member->membership == 6 && 
                    $currentDate == $halvYearExpiration &&
                    date('d-m-Y', $member->approved_date) > $halvYearExpiration 
                ) {
                    $this->memberRepository->update([ 
                        'is_payed' => 0,
                        'is_pending' => 1
                    ], $member->id);
                    
                    $logger->log("Member {$member->id}: {$member->gamertag} is now deactivated due to remaining payment, with folloing membership: 12 måneder", 4);
                    
                    $sendCanceledMail = (new MembershipCanceled($member))
                        ->setSubject("Annulleret medlemskab")
                        ->setReciever($member->email)
                        ->send();
                }
            }
        }
    }
}
?>