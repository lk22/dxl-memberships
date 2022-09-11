<?php 

    namespace DxlMembership\Classes\Views;

    use Dxl\Interfaces\ViewInterface;

    use DxlMembership\Classes\Repositories\MembershipRepository;
    use DxlMembership\Classes\Repositories\MemberRepository;
    use DxlMembership\Classes\Repositories\MembershipActivityRepository;

    if ( ! defined('ABSPATH') ) exit;

    if ( ! class_exists('MemberDetailsView') ) 
    {
        class MemberDetailsView implements ViewInterface 
        {

            /**
             * Membership repository
             *
             * @var DxlMembership\Classes\Repositories\MembershipRepository
             */
            public $membershipRepository;

            /**
             * Member repository
             *
             * @var DxlMembership\Classes\Repositories\MemberRepository
             */
            public $memberRepository;

            /**
             * Member activities repository
             *
             * @var DxlMembership\Classes\Repositories\MembershipActivityRepository
             */

            public $memberActivitiesRepository;

            /**
             * View Constructor
             */
            public function __construct() {
                $this->membershipRepository = new MembershipRepository();
                $this->memberRepository = new MemberRepository();
                $this->membershipActivityRepository = new MembershipActivityRepository();
            }

            /**
             * render the view
             *
             * @return void
             */
            public function render() {
                $member = $this->memberRepository->find((int) esc_sql($_GET["id"]));
                $membership = $this->membershipRepository->find($member->membership);
                $memberships = $this->membershipRepository->all() ?? [];
                $activities = $this->membershipActivityRepository->select()->where('member_id', $member->id)->get();
                if( $member->profile_activated ) {
                    $profile = $this->memberRepository->getProfile($member->id);
                }

                require_once ABSPATH . "wp-content/plugins/dxl-memberships/src/admin/views/details.php";
            }
        }
    }

?>