<?php 

namespace DxlMembership\Classes\Actions;
require_once(ABSPATH . "wp-content/plugins/dxl-core/src/Classes/Core.php");

use DXL\Classes\Core;
use DxlMembership\Classes\Services\MembershipService;
use DxlMembership\Classes\Requests\MembershipRequest;
use Dxl\Classes\Abstracts\AbstractAction as Action;

if(!class_exists('MembershipAction')) 
{
    class MembershipAction extends Action
    {
        /**
         * Constructor
         */
        public function __construct()
        {
            $this->dxl = new Core();
            $this->service = new MembershipService();
            $this->request = new MembershipRequest();
            $this->registerAdminActions();
            $this->registerGuestActions();
        }

        /**
         * Register membership actions
         *
         * @return void
         */
        public function registerAdminActions()
        {
            add_action("wp_ajax_dxl_create_membership", [$this, 'createMembershipAction']);
            add_action("wp_ajax_dxl_update_membership", [$this, 'updateMembershipAction']);
            add_action("wp_ajax_dxl_delete_membership", [$this, 'deleteMembershipAction']);
        }

        /**
         * register guest actions
         *
         * @return void
         */
        public function registerGuestActions() {}

        /**
         * show the view according to actions
         *
         * @return void
         */
        public function manage()
        {
            if(isset($_GET["action"])) {
                switch($_GET["action"]) 
                {
                    case "details": 
                        $this->membershipDetails();
                        break;
    
                    case "list":
                    default: 
                        $this->membershipList();
                    break;
                }
            } else {
                $this->membershipList();               
            }
        }

        /**
         * Show membership details
         *
         * @return void
         */
        public function membershipDetails()
        {
            require_once ABSPATH . "wp-content/plugins/dxl-memberships/src/admin/views/memberships/details.php";
        }

        /**
         * show membership list
         *
         * @return void
         */
        public function membershipList()
        {
            global $wpdb;
            $memberships = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "memberships");
            require_once ABSPATH . "wp-content/plugins/dxl-memberships/src/admin/views/memberships/list.php";
        }

        /**
         * Create membership 
         *
         * @return void
         */
        public function createMembershipAction()
        {
            $data = new MembershipRequest();
            $membership = $_REQUEST['membership'];
            $logger = $this->dxl->getUtility('Logger');
            $logger->log("triggering action: " . __METHOD__, 'memberships');
            
            if( ! $data->has('name') || ! $data->has('length') )
            {
                $this->dxl->response('membership', [
                    "error" => true,
                    "response" => "Could not find name or length value in request"
                ]);
                $logger->log("Request failed in action: " . __METHOD__ . " " . wp_json_encode($data->request), 'memberships');
                wp_die();
            }

            $created = $this->service->createMembership($membership);
            // $created = $this->membershipRepository->create($membership);

            if( $created < 1 ) {
                $this->dxl->response('membership', [
                    "error" => true,
                    "response" => "Noget gik galt, kunne ikke oprette kontingent"
                ]);
                $logger->log("error creating membership, " .__METHOD__ . " " . wp_json_encode($data) . "", 'memberships');
                wp_die();
            }

            $this->dxl->response('membership', [
                "response" => "kontingent " . $data->get('name') . " oprettet"
            ]);
            $logger->log("Membership " . $data->get('name') . " created successfully " . __METHOD__ . "", 'memberships');
            wp_die();
        }
    
        /**
         * Update existing membership
         *
         * @return void
         */
        public function updateMembershipAction()
        {
            $logger = $this->dxl->getUtility('Logger');
            $logger->log("triggering action: " . __METHOD__, 'memberships');

            $data = new MemberRequest();
            $membership = $data->request["membership"];

            $existingMembership = $this->service->getMembership($data->get('id'));

            if( ! $existingMembership )
            {
                $this->dxl->response('membership', [
                    "error" => true,
                    "response" => "Kunne ikke finde kontingent"
                ]);
                $logger->log("Membership not found ");
            }
            $updated = $this->service->updateMembership($membership);
        }

        /**
         * Delete existing membership
         *
         * @return void
         */
        public function deleteMembershipAction()
        {
            $request = new MembershipRequest();
            $logger = $this->dxl->getUtility('Logger');
            $logger->log("triggering action: " . __METHOD__, "memberships");
            $existingMembership = $this->service->getMembership($request->get('id'));
            
            if( ! $existingMembership ) 
            {
                $this->dxl->response('membership', [
                    "error" => true,
                    "response" => "Kunne ikke finde kontingent i systemet.",
                    "data" => $request->request["membership"]
                ]);
                $logger->log("Failed deleting membership, membership was not found, " . __METHOD__ . "", "memberships");
                wp_die();
            }

            $deleted = $this->service->deleteMembership($existingMembership->id);

            if( ! $deleted ) 
            {
                $this->dxl->resposne('membership', [
                    'error' => true,
                    "response" => "Noget gik galt, kunne ikke fjerne kontingentet fra systemet"
                ]);
                $logger->log("deleting membership failed, " . __METHOD__, "memberships");
                wp_die();
            }

            $this->dxl->response('membership', ["response" => "Kontingent " . $existingMembership->name . " er fjernet"]);
            $logger->log("membership " . $existingMembership->name . " removed " . __METHOD__, 'memberships');
            wp_die();
        }
    }
}

?>