<?php 

    namespace DxlMembership\Classes\Controllers;
    require_once(ABSPATH . "wp-content/plugins/dxl-core/src/Classes/Core.php");

    // Core
    use DXL\Classes\Core;

    // Interfaces
    use Dxl\Interfaces\ViewInterface;

    // Abstracts
    use Dxl\Classes\Abstracts\AbstractActionController as Controller;

    // Services
    use DxlMembership\Classes\Services\MemberService;
    
    // Repositories
    use DxlMembership\Classes\Repositories\MemberRepository;
    use DxlMembership\Classes\Repositories\MembershipRepository;
    use DxlMembership\Classes\Repositories\MembershipActivityRepository;
    
    // requests
    use DxlMembership\Classes\Requests\MemberRequest;
    
    // Mails
    use DxlMembership\Classes\Mails\SendRegisteredMember;
    use DxlMembership\Classes\Mails\ProfileActivated;
    use DxlMembership\Classes\Mails\ProfileDeactivated;
    use DxlMembership\Classes\Mails\MemberUserPasswordReset;
    use DxlMembership\Classes\Mails\NewMemberRequest;
    use DxlMembership\Classes\Mails\MemberRequestReceipt;

    // Views
    use DxlMembership\Classes\Views\MemberDetailsView;
    use DxlMembership\Classes\Views\MemberListView;
    
    if( !class_exists('MemberController') )
    {
        class MemberController extends Controller  
        {
            /**
             * Member Constructor
             */
            public function __construct()
            {
                $this->memberRepository = new MemberRepository();
                $this->membershipRepository = new MembershipRepository();
                $this->membershipActivityRepository = new MembershipActivityRepository();
                
                $this->memberList = new MemberListView();
                $this->memberDetails = new MemberDetailsView();

                $this->dxl = new Core();
                $this->service = new MemberService();
                $this->request = new MemberRequest();
                
                $this->registerAdminActions();
                $this->registerGuestActions();
            }

            /**
             * Register Member actions
             *
             * @return void
             */
            public function registerAdminActions()
            {
                add_action("wp_ajax_dxl_member_create", [$this, 'adminCreateMember']);
                add_action("wp_ajax_nopriv_dxl_add_member",[$this, "frontendCreateMember"]);
                add_action("wp_ajax_dxl_add_member",[$this, "frontendCreateMember"]);
                add_action("wp_ajax_dxl_member_update", [$this, 'adminUpdateMember']);
                add_action("wp_ajax_dxl_member_update_payed", [$this, 'adminAcceptPayment']);
                add_action("wp_ajax_dxl_member_deactivate_payment", [$this, 'adminDeactivatePayment']);
                add_action("wp_ajax_dxl_member_update_action", [$this, 'adminMemberActionUpdate']);
                add_action("wp_ajax_dxl_member_search", [$this, 'adminMemberSearch']);
                add_action("wp_ajax_dxl_member_delete", [$this, 'adminDeleteMember']);
                add_action('wp_ajax_dxl_export_members', [$this, 'adminExportMembers']);
            }

            public function registerGuestActions()
            {
                //add_action("wp_ajax_nopriv_dxl_add_member",[$this, "frontendCreateMember"]);
            }

            /**
             * Manage page renders
             *
             * @return void
             */
            public function manage()
            {
                if( isset($_GET["action"]) && !empty($_GET["action"]) ) 
                {
                    switch($_GET["action"])
                    {
                        case 'details': 
                            return $this->memberDetails->render();
                            break;
    
                        case 'list': 
                            return $this->memberList->render();
                            break;
                    }
                } else {
                    return $this->memberList->render();
                }
            }

            /**
             * render membership form
             *
             * @return void
             */
            public function renderMembershipForm()
            {
                global $wpdb;
                $memberships = $wpdb->get_results("SELECT id, name, price, length FROM " . $wpdb->prefix . "memberships");
                require_once ABSPATH . "wp-content/plugins/dxl-memberships/src/frontend/views/create.php";
            }

            /**
             * Get latest members list
             *
             * @return void
             */
            public function getLatest()
            {
                $members = $this->memberRepository->latest(10);
                $memberData = [];

                foreach($members as $m => $member) {
                    $membership = $this->membershipRepository->select(['name'])->where('id', $member->membership)->getRow();
                    $memberData[$m] = [
                        "name" => $member->name,
                        "gamertag" => $member->gamertag,
                        "membership" => $membership->name ?? ''
                    ];
                }
                return $memberData;
            }

            /**
             * Get awaiting members
             *
             * @return void
             */
            public function getAwaitingMembers()
            {
                return $this->memberRepository->getAwaiting();
            }

            /**
             * creating new member resource from admin
             *
             * @return void
             */
            public function adminCreateMember()
            {
                $existingMember = $this->service->getMember($_REQUEST["member"]["gamertag"]);
                
                if( !$existingMember > 0 && $_REQUEST["member"]["accept"] == "on" ) {
                    $member = $this->service->createMember([
                        "member_number" => $_REQUEST["member"]["member_number"],
                        "user_id" => 0,
                        "name" => $_REQUEST["member"]["name"],
                        "gamertag" => $_REQUEST["member"]["gamertag"],
                        "birthyear" => $_REQUEST["member"]["birthyear"],
                        "email" => $_REQUEST["member"]["email"],
                        "phone" => $_REQUEST["member"]["phone"],
                        "gender" => $_REQUEST["member"]["gender"],
                        "address" => $_REQUEST["member"]["address"],
                        "city" => $_REQUEST["member"]["city"],
                        "zipcode" => $_REQUEST["member"]["zipcode"],
                        "municipality" => $_REQUEST["member"]["municipality"],
                        "is_pending" => 1,
                        "is_payed" => 0,
                        "membership" => $_REQUEST["member"]["membership"],
                        "profile_activated" => 0,
                        "auto_renew" => 1,
                        "approved_date" => 0,
                        "created_at" => strtotime('now', time())
                    ]);

                    if( isset($member["validation"]) && $member["validation"] == false ) {
                        echo wp_json_encode([
                            "member" => [
                                "validation" => "Validering fejlede, kunne ikke oprette medlemmet: " . $_REQUEST["member"]["name"]
                            ]
                        ]);
                    }

                    if( $member > 0 ) {

                        $membership = $this->membershipRepository->find($_REQUEST["member"]["membership"]);

                        // send member requuest receipt mail to created member
                        $mail = (new MemberRequestReceipt($member, $membership))
                            ->setSubject("Nyt medlemskab - " . $membership->name)
                            ->setReciever($_REQUEST["member"]["email"])
                            ->send();

                        echo wp_json_encode( [
                            "member" => [
                                "response" => "" . $_REQUEST["member"]["name"] . "er oprettet med success",
                                ]
                            ]);
                        wp_die();
                    }
                }


                echo wp_json_encode( ["member" => ["response" => "Medlemmet eksistere allerede i registeret"]]);
                wp_die();
            }

            /**
             * exporting all member into 2 sheets 
             *
             * @return void
             */
            public function adminExportMembers()
            {
                global $wpdb;
                $logger = $this->dxl->getUtility('Logger');

                $payedMembers = $wpdb->get_results($wpdb->prepare(
                    "SELECT 
                        m.*,
                        m.is_payed,
                        m.membership,
                        ms.id,
                        ms.name AS membership_name
                    FROM " . $wpdb->prefix . "members AS m
                    INNER JOIN " . $wpdb->prefix . "memberships AS ms 
                    ON m.membership = ms.id
                    WHERE m.is_payed IN(%d)", 
                    1
                ));
    
                $notPayedMembers = $wpdb->get_results($wpdb->prepare(
                    "SELECT 
                        m.*,
                        m.is_payed,
                        m.membership,
                        ms.id,
                        ms.name AS membership_name
                    FROM " . $wpdb->prefix . "members AS m
                    INNER JOIN " . $wpdb->prefix . "memberships AS ms 
                    ON m.membership = ms.id
                    WHERE m.is_payed IN(%d)", 
                    0
                ));

                $exported = $this->service->exportMembers($payedMembers, $notPayedMembers);

                // echo wp_json_encode($exported); wp_die();
                $logger->log("exported data: " . wp_json_encode($exported) . " " . __METHOD__, 'memberships');
                $this->dxl->response('members',["response" => $exported]); wp_die();
                if( !$exported ) {
                    $this->dxl->response('member', [
                        'error' => true,
                        'response' => "Noget gik galt, kunne ikke exportere"    
                    ]);
                    $logger->log("tried to export members, something went wrong", "memberships");
                    wp_die();
                }

                $this->dxl->response('members', [
                    "response" => $exported
                ]);
                $logger->log("exported following list: " . wp_json_encode($exported), 'memberships');
                wp_die();
            }

            /**
             * registering new member from frontend form
             *
             * @return void
             */
            public function frontendCreateMember()
            {
                $logger = $this->dxl->getUtility('Logger');
                $existingMember = $this->service->getMember($_REQUEST["member"]["gamertag"]);

                if( $existingMember && $existingMember->gamertag == $_REQUEST["member"]["gamertag"] ) 
                {
                    $this->dxl->response('member', [
                        "error" => true,
                        "response" => "Dette gamertag eksistere allerede"
                    ]);
                    $logger->log("creating new member failed, gamertag allready exists, " . __METHOD__, 'memberships');
                    wp_die();
                }

                // echo json_encode($_REQUEST["member"]);
                // wp_die();

                $birth = $_REQUEST["member"]["birthyear"] . "-" . $_REQUEST["member"]["birthdate_day"] . "-" . $_REQUEST["member"]["birthdate_month"];

                $created = $this->service->createMember([
                    "member_number" => $existingMember->member_number + 1,
                    "user_id" => 0,
                    "name" => $_REQUEST["member"]["name"],
                    "gamertag" => $_REQUEST["member"]["gamertag"],
                    "birthyear" => $_REQUEST["member"]["birthyear"],
                    "email" => $_REQUEST["member"]["email"],
                    "phone" => $_REQUEST["member"]["phone"],
                    "gender" => $_REQUEST["member"]["gender"],
                    "address" => $_REQUEST["member"]["address"],
                    "city" => $_REQUEST["member"]["city"],
                    "zipcode" => $_REQUEST["member"]["zipcode"],
                    "municipality" => $_REQUEST["member"]["municipality"],
                    "is_pending" => 1,
                    "is_payed" => 0,
                    "membership" => $_REQUEST["member"]["membership"],
                    "profile_activated" => 0,
                    "auto_renew" => $_REQUEST["member"]["auto_renewal"],
                    "approved_date" => 0,
                    "created_at" => strtotime('now', time())
                ]);

                if( !$created ) {
                    $this->dxl->response('member', [
                        "error" => true,
                        "response" => "Noget gik galt, kunne ikke oprette medlem"
                    ]); 
                    $logger->log("Failed to create new member from frontend, " . json_encode($_REQUEST["member"]) . " " . __METHOD__, 'memberships');
                    wp_die();
                }

                $membership = $this->membershipRepository->find($_REQUEST["member"]["membership"]);

                $member = $this->memberRepository->find($created); // should give you the new member
        
                // send new member request to admin
                $newMemberReceipt = (new NewMemberRequest($member, $membership))
                    ->setSubject("Nyt medlemskab")
                    ->setReciever("medlemskab@danishxboxleague.dk")
                    ->send();

                // send receipt to new member
                $memberRequestReceipt = (new MemberRequestReceipt($member, $membership))
                    ->setSubject("Kvittering medlemskab - " . $membership->name)
                    ->setReciever($member->email)
                    ->send();

                $this->dxl->response('member', ["response" => "Du er nu oprettet i vores system og vil blive taget hånd om dit medlemsskab"]);
                wp_die();
            }

            /**
             * Updating existing member
             *
             * @return void
             */
            public function adminUpdateMember() 
            {
                $existingMember = $this->service->getMemberById($_REQUEST["member"]["id"]);

                if( !$existingMember ) {
                    $this->dxl->response('member', [
                        "error" => true,
                        "response" => "Kunne ikke finde medlemsdata at opdatere.",
                        "data" => $_REQUEST["member"]
                    ]);

                    $this->dxl->log("Failed to update member, could not fetch member data" . wp_json_encode($_REQUEST["member"]), 'memberships', 2);
                    wp_die();
                }

                $updated = $this->service->updateMember([
                    "member_number" => $_REQUEST["member"]["member_number"],
                    "name" => $_REQUEST["member"]["name"],
                    "gamertag" => $_REQUEST["member"]["gamertag"],
                    "birthyear" => $_REQUEST["member"]["birthyear"],
                    "email" => $_REQUEST["member"]["email"],
                    "phone" => $_REQUEST["member"]["phone"],
                    "gender" => $_REQUEST["member"]["gender"],
                    "address" => $_REQUEST["member"]["address"],
                    "city" => $_REQUEST["member"]["city"],
                    "zipcode" => $_REQUEST["member"]["zipcode"],
                    "municipality" => $_REQUEST["member"]["municipality"],
                    "membership" => $_REQUEST["member"]["membership"],
                    "auto_renew" => $_REQUEST["member"]["auto_renew"],
                    "updated_at" => time()
                ], [
                    "id" => $existingMember->id
                ]);

                if( !$updated ) {
                    $this->dxl->response('member', [
                        "error" => true,
                        "response" => "Noget gik galt, kunne ikke opdatere medlem",
                        "data" => $_REQUEST["member"]
                    ]);

                    $this->dxl->log("Failed to update member, could not fetch member data" . wp_json_encode($_REQUEST["member"]), 'memberships', 2);
                    wp_die();
                }

                $this->dxl->response('member', [
                    "response" => "Medlem: " . $_REQUEST["member"]["name"] . " er opdateret"
                ]);
                $this->dxl->log("Updated member information" . wp_json_encode($_REQUEST["member"]), "memberships");
                wp_die();
            }

            public function adminDeleteMember()
            {
                $logger = $this->dxl->getUtility('Logger');
                $logger->log("Triggering event: " . __METHOD__, 'memberships');

                $member = (isset($_REQUEST["member"]["id"])) ? $this->service->getMemberById($_REQUEST["member"]["id"]) : false;
                if( false == $member ) {
                    $this->dxl-response('member', [
                        "error" => true,
                        "response" => "Noget gik galt, kunne ikke finde medlems ID"
                    ]);
                    $logger->log("tried to delete member, could not find member ID " . __METHOD__, 'memberships');
                    wp_die();
                }

                $deleted = $this->service->removeMember($member->id);

                if( $deleted == false ) {
                    $this->dxl->response('member', [
                        "error" => true,
                        "response" => "Noget gik galt, kunne ikke fjerne medlem"
                    ]);
                    $logger->log("tried to delete member, something went wrong", 'memberships');
                    wp_die();
                }

                $this->dxl->response('member', [
                    'resposne' => 'medlem fjernet'
                ]);
                $logger->log("removed member, " . $member->name . " (" . $member->gamertag . ") " . __METHOD__, 'memberships');
                wp_die();
            }

            /**
             * Accept member payment and change payed status to payed
             *
             * @return void
             */
            public function adminAcceptPayment()
            {
                global $current_user;
                $verified = $this->request->verify_nonce();

                $member_id = $_REQUEST["member"]["id"];
                if( !$member_id ) {
                    $this->dxl->response('member', [
                        "error" => true,
                        "response" => "kunne ikke finde medlems ID i din forespørgsel."
                    ]);
                    $this->dxl->log("could not get requested member ID in action: " . __METHOD__, 'memberships', 2);
                    wp_die();
                }

                $member = $this->service->getMemberById($member_id);
                
                $this->dxl->log("Activating member " . $member->gamertag . " " . __METHOD__, "memberships");
                $user = wp_create_user($member->gamertag, $member->gamertag, $member->email);
            
                if( isset($user->errors) ) {
                    $this->dxl->response('member', [
                        "error" => true,
                        "response" => "bruger oprettelse fejlede, bruger eksistere allerede"
                    ]);

                    $this->dxl->log("tried to create user for member: " .$member->gamertag . " from action: " . __METHOD__ . " " . wp_json_encode($user), 'memberships', 2);
                    wp_die();
                }
                $this->dxl->log("Accepting payment for member: " . $member->gamertag . " " . __METHOD__, "memberships");
                $accepted = $this->service->acceptPayed($member->id, $user);
                
                if( $accepted == false ) {
                    $this->dxl->response('member', [
                        "error" => true,
                        "response" => "Noget gik galt, kunne ikke betalings status"
                    ]);

                    $this->dxl->log("tried to update member: " . $member->gamertag ."(" . $member->gamertag . ") from action: " . __METHOD__, 'memberships', 2);
                    wp_die();
                }

                $this->dxl->response('member', [
                    "response" => "Medlem er aktiveret"
                ]);
                
                $this->dxl->log("Member payment status updated successfully", 'memberships');
                wp_die();
            }

            /**
             * Deactivate member payment
             *
             * @return void
             */
            public function adminDeactivatePayment()
            {
                global $current_user;
                $this->dxl->log("Triggering action: " . __METHOD__, 'memberships');
                
                $member_id = $_REQUEST["member"]["id"];
                $cancelReason = $_REQUEST["member"]["reason"];
                
                if( !$member_id ) 
                {
                    $this->dxl->response('member', ["error" => true, "response" => "Kunnde ikke finde medlems ID i din forespørgsel"]);
                    $this->dxl->log("Tried to deactivate member payment status", "memberships", 2);
                    wp_die();
                }

                $member = $this->service->getMemberById($member_id);
                $userRemoved = wp_delete_user($member->user_id);
                
                // @TODO: fix user detaching while deactivating members
                if( !$userRemoved ) 
                {
                    // $this->dxl->response('member', [
                    //     "error" => true,
                    //     "response" => "Noget gik galt, kunne ikke fjerne bruger login tilknyttet " . $member->gamertag
                    // ]);
                    $this->dxl->log("Tried to remove attached login user on " . $member->gamertag . " " . wp_json_encode($_REQUEST["member"]), "memberships");
                    // wp_die();
                }
                
                $deactivated = $this->service->deactivateMember($member->id, $cancelReason);

                $this->dxl->log("Deactivating member: " . $member->name . " in action " . __METHOD__, 'memberships', 1);

                if( $deactivated == false ) 
                {
                    $this->dxl->response("member", [
                        "error" => true,
                        "response" => "Noget gik galt, kunne ikke deaktivere medlem"
                    ]);

                    $this->dxl->log("Could not deactivate member " . $member->name . " " . wp_json_encode($_REQUEST["member"]), "memberships", 2);
                    wp_die();
                }

                $this->dxl->response('member', [
                    "response" => $member->gamertag . " er deaktiveret"
                ]);

                $this->dxl->log("Member: " . $member->gamertag . " deactivated", "memberships");
                wp_die();
            }

            /**
             * Member search action
             *
             * @return void
             */
            public function adminMemberSearch() 
            {
                $field = $_REQUEST["member"]["field"]["name"];
                $value = $_REQUEST["member"]["field"]["fieldValue"];

                $members = $this->service->getSearchedMembers($field, $value);

                $this->dxl->response('member', [
                    "response" => "Der findes " . count($members) . " medlemmer udfra din søgning",
                    "data" => $members
                ]);
                $this->dxl->log("There was found " . count($members) . " members in search action: " . __METHOD__, "memberships");
                wp_die();
            }

            /**
             * Execute member update actions
             *
             * @return void
             */
            public function adminMemberActionUpdate()
            {
                $this->dxl->log("Triggering action " . __METHOD__, "memberships");
                $action = $_REQUEST["member"]["action"];
                
                if( !$_REQUEST["member"]["id"] ) {
                    $this->dxl->response('member', [
                        "error" => true,
                        "response" => "Noget gik galt, kunne ikke finde medlem i din forespørgsel"
                    ]);
                    wp_die();
                }

                $member = $this->service->getMemberById($_REQUEST["member"]["id"]);
                if($member == false) {
                    $this->dxl->response('member', [
                        "error" => true,
                        "response" => "Fandt ikke medlemsdata i processen"
                    ]);
                    $this->dxl->log("Could not find member ressource from action: " . __METHOD__, 'memberships', 2);
                    wp_die();
                }

                switch( $action ) {
                    /**
                     * give member access to use the profile dashboard attached to the member
                     * @deprecated version 1.0.0 - 11-02-2023
                     */
                    case "activate-profile":
                        $this->dxl->log("Triggering update action: 'activate-profile' " . __METHOD__, "memberships");
                        $activated = $this->service->activateMemberProfile();

                        if( $activated == false or $activated == 0 ) {
                            $this->dxl->response('member', [
                                "error" => true,
                                "response" => "Noget gik galt, kunne ikke aktivere " . $member->gamertag . "'s profil",
                            ]);
                            $this->dxl->log("Could not activate " . $member->gamertag . "'s profile dashboard", "memberships");
                            wp_die();
                        }
                        
                        $mail = (new ProfileActivated($member));
                        $mail->setReciever("knudsenudvikling@gmail.com")
                        ->setSubject("Profil aktiveret")
                        ->send();
                        
                        $this->dxl->response('member', [
                            "response" => $member->gamertag . "'s profil er aktiveret."
                        ]);
                        $this->dxl->log("Activated " . $member->gamertag . "'s profile dashboard successfully", "memberships");
                        
                        
                        wp_die();
                        break;

                    /**
                     * Remove access to member attached profile dashboard
                     * @deprecated version 1.0.0 - 11-02-2023
                     */
                    case "deactivate-profile":
                        $this->dxl->log("Triggering update action: 'deactive-profile' " . __METHOD__, "memberships");
                        $deactivated = $this->service->deactivateMemberProfile();

                        if( $deactivated == false ) {
                            $this->dxl->response('member',[
                                "error" => true,
                                "response" => "Noget gik galt, kunne ikke deaktivere " . $member->gamertag . "'s profil"
                            ]);
                            $this->dxl->log("Could not deactivate member profile, data: " . wp_json_encode($_REQUEST["member"]), "memberships");
                            wp_die();
                        }

                        $this->dxl->response('member', [
                            "response" => $member->gamertag . "'s profil er deaktiveret"
                        ]);

                        $this->dxl->log("Member profile deactivated, member: " . wp_json_encode(["member" => ["name" => $member->name, "gamertag" => $member->gamertag]]), "memberships");
                        
                        $mail = (new ProfileDeactivated($member));
                        $mail->setReciever("knudsenudvikling@gmail.com")
                            ->setSubject("Profil deaktiveret")
                            ->send();
                        
                        wp_die();

                        break;

                    /**
                     * Assign trainer permissions to member
                     */
                    case 'assign-trainer-permissions':
                        $this->dxl->log("Triggering update action: 'assign-trainer-permissions' " . __METHOD__, "memberships");
                        $assigned = $this->service->assignTrainerPermissions();

                        if( $assigned == false || $assigned == 0 ) {
                            $this->dxl->response('member', [
                                "error" => true,
                                "response" => "Noget gik galt, kunne ikke gøre " . $member->gamertag . " til træner", 
                            ]);

                            $this->dxl->log("could not assign trainer permissions, requested data: " . wp_json_encode($_REQUEST["member"]), "memberships", 2);
                            wp_die();
                        } 

                        $this->dxl->response('member', [
                            "response" => $member->gamertag . " er nu træner"
                        ]);

                        $this->dxl->log($member->gamertag . " trainer permissions assigned", "memberships");
                        
                        wp_die();

                        break;

                    /**
                     * remove assigned trainer permissions from member
                     */
                    case 'remove-trainer-permissions': 
                        $this->dxl->log("Triggering update action: 'remove-trainer-permissions' " . __METHOD__, "memberships");
                        $removed = $this->service->removeTrainerPermissions();

                        if ( $removed == false || $removed == 0) {
                            $this->dxl->response('member', [
                                "error" => true,
                                "response" => "Noget gik galt, kunne ikke fjerne rettigheder"
                            ]);

                            $this->dxl->log("Could not remove trainer permission on member: " . $member->gamertag . "data: " . wp_json_encode($_REQUEST["member"]), "memberships");
                            wp_die();
                        }

                        $this->dxl->response('member', [
                            "response" => "Træner rettigheder fjernet"
                        ]);

                        $this->dxl->log("Removed trainer permissions on following member: " . $member->gamertag, "memberships");
                        wp_die();
                        break;

                    /**
                     * Assigning tournament permissions to member
                     */
                    case 'assign-tournament-permissions':
                        $this->dxl->log("Triggering update action: 'assign-tournament-permissions' " . __METHOD__, "memberships");
                        $assigned = $this->service->assignTournamentPermissions();

                        if( $assigned == false || $assigned == 0 ) {
                            $this->dxl->response('member', [
                                "error" => true,
                                "response" => "Noget gik galt, kunne ikke give give rettigheder til " . $member->gamertag
                            ]);
                            $this->dxl-log("Could not give tournament author permissions", "memberships", 2);
                            wp_die();
                        }

                        $this->dxl->response('member', [
                            "response" => $member->gamertag . " har fået rettigheder som turnerings ansvarlig"
                        ]);

                        $this->dxl->log("Gave tournament author permissions to member: " . $member->gamertag, 'memberships');
                        wp_die();
                        break;

                    case 'remove-tournament-permissions':
                        $this->dxl->log("Triggering update action: 'remove-tournament-permissions' " . __METHOD__, "memberships");
                        $removed = $this->service->removeTournamentPermissions();

                        if( $removed == false || $removed == 0 ) {
                            $this->dxl->response('member', [
                                "error" => true,
                                "response" => "Noget gik galt, kunne ikke give give rettigheder til " . $member->gamertag
                            ]);
                            $this->dxl-log("Could not remove tournament author permissions", "memberships", 2);
                            wp_die();
                        }

                        $this->dxl->response('member', [
                            "response" => $member->gamertag . " har fået fjernet rettigheder som turnerings ansvarlig"
                        ]);
                        $this->dxl->log("Removed tournament author permissions to member: " . $member->gamertag, 'memberships');
                        wp_die();

                        break;

                    case 'reset-member-password':

                        $this->dxl->log('Triggering update action: "reset-member-pwd" ' . __METHOD__, "memberships");
                        $this->service->resetMemberPassword($member); // assign to gamertag

                        $mail = (new MemberUserPasswordReset($member))->setSubject($member->gamertag . " - nulstillet adgangskode")
                            ->setReciever($member->email)
                            ->send();

                        $this->dxl->response('member', [
                            "response" => "Adgangskode er nulstillet"
                        ]);

                        wp_die();
                        break;
                }
            }
        }
    }
?>