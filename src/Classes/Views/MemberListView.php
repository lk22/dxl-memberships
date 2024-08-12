<?php 

    namespace DxlMembership\Classes\Views;

    use Dxl\Interfaces\ViewInterface;

    use DxlMembership\Classes\Repositories\MembershipRepository;
    use DxlMembership\Classes\Repositories\MemberRepository;

    if ( ! defined('ABSPATH') ) exit;

    if ( ! class_exists('MemberListView') ) 
    {
        class MemberListView implements ViewInterface 
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
             * View Constructor
             */
            public function __construct( 
            ) {
                $this->membershipRepository = new MembershipRepository();
                $this->memberRepository = new MemberRepository();
            }

            /**
             * render the view
             *
             * @return void
             */
            public function render() 
            {
                global $wpdb;
                $members = $this->memberRepository->select()
                    ->where('is_payed', 1)
                    ->descending('member_number')
                    ->get();
                $notPayedMembers = $this->memberRepository->select()
                    ->where('is_payed', 0)
                    ->descending('member_number')
                    ->get();
                $memberships = $this->membershipRepository->all();

                require_once ABSPATH . "wp-content/plugins/dxl-memberships/src/admin/views/list.php";
            }
        }
    }

?>