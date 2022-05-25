<?php 
namespace DxlMembership\Classes\Services;

if( !class_exists('MembershipService') )
{
    class MembershipService
    {

        /**
         * primary identifier property
         *
         * @var string
         */
        protected $primaryIdentifier = "id";
        
        /**
         * fetch single membership record
         *
         * @param [type] $identifier
         * @return void
         */
        public function getMembership($membership) 
        {
            global $wpdb;
            return $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM " . $wpdb->prefix . "memberships WHERE id IN(%d)", $membership
            ));
        }

        /**
         * create new membership resource
         *
         * @param [type] $data
         * @return void
         */
        public function createMembership($data)
        {
            global $wpdb;
            $created = $wpdb->insert($wpdb->prefix . "memberships", $data);
            return ($created) ? $wpdb->insert_id : false;
        }

        /**
         * Update existing membership record
         *
         * @param [type] $data
         * @param [type] $membership
         * @return void
         */
        public function updateMembership($data, $membership)
        {
            global $wpdb;
            $updated = $wpdb->updated($wpdb->prefix . "memberships", $data, [$this->primaryIdentifier => $membership]);
            return ($updated) ? true : false;
        }

        /**
         * delete existing membership resource
         *
         * @param [type] $membership
         * @return void
         */
        public function deleteMembership($membership) 
        {
            global $wpdb;
            $deleted = $wpdb->delete($wpdb->prefix . "memberships", [$this->primaryIdentifier => $membership]);
            return ($deleted) ? true : false;
        }
    }
}

?>