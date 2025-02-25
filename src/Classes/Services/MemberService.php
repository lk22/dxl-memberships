<?php 
namespace DxlMembership\Classes\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use DxlMembership\Classes\Repositories\MembershipActivityRepository;

if( ! defined('ABSPATH') ) exit;

if( ! class_exists('MemberService') )
{
    class MemberService 
    {
        /**
         * Data Sanitizer object
         */
        public $sanitizer;

        /**
         * Primary identifier to update and delete purposes
         *
         * @var string
         */
        public $primaryIdentifier = 'id';

        /**
         * Membership activity repository
         *
         * @var DxlMembership\Classes\Repositories\MembershipActivityRepository
         */
        public $membershipActivityRepository;

        /**
         * Member Service Constructor
         */
        public function __construct()
        {
            $this->membershipActivityRepository = new MembershipActivityRepository;
            //$this->sanitizer = new Sanitizer('MemberSanitizer');
        }

        /**
         * Setter for primary column identifier
         *
         * @param [type] $identifier
         * @return void
         */
        public function setPrimaryIdentifier($identifier)
        {
            $this->primaryIdentifier = $primaryIdentifier;
        }

        /**
         * Setter for custom Sanitizer to sanitize member data
         *
         * @param [type] $sanitizer
         * @return void
         */
        public function setSanitizer($sanitizer) {
            $this->sanitizer = $sanitizer;
        }

        /**
         * fetching existing member resource
         *
         * @param [type] $member
         * @param string $fields
         * @return void
         */
        public function getMember($member, $fields = "*") {
            global $wpdb;

            if(is_array($fields)) {
                $fields = implode(',', $fields);
            }

            $returnedMember = $wpdb->get_row($wpdb->prepare("SELECT $fields FROM " . $wpdb->prefix . "members WHERE gamertag IN(%s)", $member));
            return $returnedMember;
        }

        /**
         * get member record by specific identifier
         *
         * @param [type] $member
         * @param string $fields
         * @return void
         */
        public function getMemberById($member)
        {
            global $wpdb;
            return $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM " . $wpdb->prefix . "members WHERE id IN(%d)",
                    (int) $member
                ),
            );
        }

        public function getNotPayedMembers($field = '*')
        {
            global $wpdb;
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM " . $wpdb->prefix . "members WHERE is_payed IN(%d)", 0
            ));
        }

        public function getPayedMembers($field = '*')
        {
            global $wpdb;
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM " . $wpdb->prefix . "members WHERE is_payed IN(%d)", 1
            ));
        }

        /**
         * Creating new member resource
         *
         * @param [type] $member
         * @return void
         */
        public function createMember($member)
        {
            global $wpdb;

            // @todo make sanitizing of requested data resource
            // $validated = (new MemberValidator($member))->validate();
            if(is_array($validated) && isset($validated["validation"])) {
                return $validated["validation"];
            }

            $wpdb->insert($wpdb->prefix . "members", $member);
            return $wpdb->insert_id;
        }

        /**
         * Trashing member resource
         *
         * @param [type] $member
         * @return void
         */
        public function trashMember($member)
        {
            // validate on Core settings defined in Core module if a member can be trashed
            // can be used for statistic reasons

            if( get_option('dxl_allow_members_trashables') ) {
                $this->update(
                    ["is_trashed" => 1], 
                    [$this->primaryIdentifier => (is_array[$member]) ? $member[$primaryIdentifer] : $member->{$this->primaryIdentifer}]);
            }
        }

        /**
         * removing an member
         *
         * @param [type] $member
         * @param string $field
         * @return void
         */
        public function removeMember($member, $field = "id")
        {
            global $wpdb;

            $deleted = $wpdb->delete($wpdb->prefix . "members", [$this->primaryIdentifier => (int) $member]);

            if( ! $deleted ) return false;

            $wpdb->delete($wpdb->prefix . "memberships_activity", ["member_id" => (int) $member]);

            return true;
        }

        /**
         * updating existing member resource
         *
         * @param [type] $member
         * @param [type] $where
         * @return void
         */
        public function updateMember(array $member, array $where = [])
        {
            global $wpdb;
            $updated = $wpdb->update($wpdb->prefix . "members", $member, $where);
            return (!$updated || $updated == 0) ? false : true;
        }

        /**
         * accept payment and update status
         *
         * @param [type] $member
         * @return void
         */
        public function acceptPayed($member, $user_id)
        {
            global $wpdb;
            
            $updated = $wpdb->update($wpdb->prefix . "members", [
                "is_payed" => 1,
                "profile_activated" => 1,
                "user_id" => (int) $user_id,
                "approved_date" => time() // -- current day
            ], [$this->primaryIdentifier => $member]);

            if( ! $updated ) {
                return false;
            }

            $this->membershipActivityRepository->create([
                "member_id" => $member,
                "status" => "Kontingent faktura betalt",
                "status_message" => "Kontingent på medlem er registreret betalt",
                "created_at" => time()
            ]);

            return true;
        }

        /**
         * Deactivating member resource 
         *
         * @param [type] $member
         * @return void
         */
        public function deactivateMember($member, string $reason = "") : bool
        {
            global $wpdb;

            $updated = $wpdb->update($wpdb->prefix . "members", [
                "is_payed" => 0,
                "profile_activated" => 0,
                "user_id" => 0,
            ], [$this->primaryIdentifier => $member]);

            if ( ! $updated ) {
                return false;
            }

            $this->membershipActivityRepository->create([
                "member_id" => $member,
                "status" => "Anulleret",
                "status_message" => ( ! is_null($reason) ) ? $reason : "Kontingent på medlem er registreret anulleret",
                "created_at" => time()
            ]);

            return true;
        }

        /**
         * returns requests search members
         *
         * @param [type] $value
         * @return void
         */
        public function getSearchedMembers($field, $value) 
        {
            global $wpdb;
            $members = $wpdb->get_results("SELECT DISTINCT * FROM " . $wpdb->prefix . "members WHERE " . $field . " LIKE '%$value%'");
            return $members;
        }

        /**
         * activating member profile status
         *
         * @return void
         */
        public function activateMemberProfile()
        {
            global $wpdb;
            $activated = $wpdb->update($wpdb->prefix . "members", ["profile_activated" => 1], [
                $this->primaryIdentifier => (int) $_REQUEST["member"][$this->primaryIdentifier]
            ]);

            $wpdb->insert($wpdb->prefix . "member_profile_settings", [
                "member_id" => (int) $_REQUEST["member"][$this->primaryIdentifier],
                "trainer_permissions_requested" => 0, 
                "tournament_permissions_requested" => 0,
                "is_trainer" => 0,
                "is_tournament_author" => 0,
                "redirect_to_manager" => 1,
                "theme" => "default"
            ]);

            return $activated;
        }

        /**
         * Deactivating member profile status
         *
         * @return void
         */
        public function deactivateMemberProfile()
        {
            global $wpdb;

            $deactivated = $wpdb->update($wpdb->prefix . "members", [
                "profile_activated" => 0
            ], [
                $this->primaryIdentifier => (int) $_REQUEST["member"][$this->primaryIdentifier]
            ]);
            
            $wpdb->delete($wpdb->prefix . "member_profile_settings", [
                "member_id" => (int) $_REQUEST["member"][$this->primaryIdentifier]
            ]);
            
            return (!$deactivated || $deactivated == 0) ? false : true;
        }

        /**
         * Assigning trainer permissions
         *
         * @return void
         */
        public function assignTrainerPermissions()
        {
            global $wpdb;

            $profile = $this->getProfile((int) $_REQUEST["member"][$this->primaryIdentifier]);

            $assigned = $wpdb->update($wpdb->prefix . "member_profile_settings", [
                "is_trainer" => 1,
                "trainer_permissions_requested" => 0 // resseting request
            ], [
                "member_id" => $profile->id
            ]);

            return (!$assigned || $assigned == 0) ? false : true;
        }

        /**
         * removing the trainer permissions
         *
         * @return void
         */
        public function removeTrainerPermissions()
        {
            global $wpdb;

            $profile = $this->getProfile((int) $_REQUEST["member"][$this->primaryIdentifier]);

            $removed = $wpdb->update($wpdb->prefix . "member_profile_settings", [
                "is_trainer" => 0
            ], ["member_id" => $profile->id]);

            return (!$removed || $removed == 0) ? false : true;
        }

        /**
         * assigning tournament author permissions
         *
         * @return void
         */
        public function assignTournamentPermissions()
        {
            global $wpdb;

            $profile = $this->getProfile((int) $_REQUEST["member"][$this->primaryIdentifier]);

            $assigned = $wpdb->update($wpdb->prefix . "member_profile_settings", [
                "is_tournament_author" => 1,
                "tournament_permissions_requested" => 0
            ], [
                "member_id" => $profile->id
            ]);

            return (!$assigned || $assigned == 0) ? false : true;
        }

        /**
         * Removing permissions as a tournament author
         *
         * @return void
         */
        public function removeTournamentPermissions()
        {
            global $wpdb;

            $profile = $this->getProfile((int) $_REQUEST["member"][$this->primaryIdentifier]);

            $removed = $wpdb->update($wpdb->prefix . "member_profile_settings", [
                "is_tournament_author" => 0
            ], ["member_id" => $profile->id]);

            return (!$removed || $removed == 0) ? false : true;
        }

        /**
         * Reseting password for a given member
         *
         * @param [type] $member
         * @return void
         */
        public function resetMemberPassword($member) 
        {
            $user = new \WP_User($member->user_id);

            return wp_set_password(
                $member->gamertag,
                $user->ID
            );
        }

        /**
         * exporting all members
         *
         * @param [type] $sheet
         * @param [type] $writer
         * @return void
         */
        public function exportMembers(
            $payedMembers,
            $notPayedMembers
        ) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet()->setTitle("Betalte Medlemmer");
            $notPayedMembersSpreadsheet = clone $spreadsheet->getActiveSheet();
            $notPayedMembersSpreadsheet->setTitle("Ikke betalte medlemmer");
            $spreadsheet->addSheet($notPayedMembersSpreadsheet);

            // define field labels
            $sheet->setCellValue("A1", "DXL medlemmer")->getColumnDimension("A")->setAutoSize(true);
            $sheet->setCellValue("A2", "E-mail")->getColumnDimension("A")->setAutoSize(true);
            $sheet->setCellValue("B2", "Køn")->getColumnDimension("B")->setAutoSize(true);

            $styles = [
                "font" => [
                    "bold" => true,
                    "size" => "20",
                    // "color" => "ffffff"
                ],
                "fill" => [
                    "fillType" => Fill::FILL_SOLID,
                    "color" => [
                        "argb" => "2ecc71"
                    ]
                ]
            ];

            $sheet->getStyle('A1:M1')->applyFromArray($styles);
            $notPayedMembersSpreadsheet->getStyle('A1:M1')->applyFromArray($styles);

            $notPayedMembersSpreadsheet->setCellValue("A1", "DXL afventende medlemmer")->getColumnDimension("A")->setAutoSize(true);
            $notPayedMembersSpreadsheet->setCellValue("A2", "E-mail")->getColumnDimension("J")->setAutoSize(true);
            $notPayedMembersSpreadsheet->setCellValue("B2", "Køn")->getColumnDimension("D")->setAutoSize(true);

            $styles["font"]["size"] = "16";
            $sheet->getStyle('A2:B2')->applyFromArray($styles); 
            $notPayedMembersSpreadsheet->getStyle('A2:B2')->applyFromArray($styles);

            
            $row = 3;
            foreach($payedMembers as $member) 
            {   
                $sheet->setCellValue("A{$row}", $member->email)->getColumnDimension('J')->setAutoSize(true);
                $sheet->setCellValue("B{$row}", $member->gender)->getColumnDimension('D')->setAutoSize(true);

                $row++;
            }

            /**
             * looping through all not payed members
             */
            $notPayedMemberRow = 3;
            foreach($notPayedMembers as $member) {
                $notPayedMembersSpreadsheet->setCellValue("A{$notPayedMemberRow}", $member->email)->getColumnDimension('J')->setAutoSize(true);
                $notPayedMembersSpreadsheet->setCellValue("B{$notPayedMemberRow}", $member->gender)->getColumnDimension('D')->setAutoSize(true);

                $notPayedMemberRow++;
            }

            $writer = new Xlsx($spreadsheet);
            // return "test";

            $filename = ABSPATH . "wp-content/plugins/dxl-memberships/CSV/member_data_" . date("d_m_Y", strtotime('today')) . ".xlsx";
            $writer->save($filename);
            // return "test";

            return str_replace(ABSPATH, "", $filename);
        }

        /**
         * fetch member
         *
         * @param [type] $member
         * @return void
         */
        private function getProfile($member)
        {
            global $wpdb;

            return $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM " . $wpdb->prefix . "members WHERE id IN(%d)",
                $member
            ));
        }
    }
}


?>