<?php 
namespace DxlMembership\Classes\Mails;

use Dxl\Classes\Abstracts\AbstractMailer as Mailer;

if( !class_exists('ProfileDeactivated') )
{
    class ProfileDeactivated extends Mailer {

        /**
         * Member data
         *
         * @var \DxlMembership\Classes\Member
         */
        public $member;

        /**
         * Constructor
         *
         * @param \DxlMembershipClasses\Member $member
         */
        public function __construct($member)
        {
            $this->member = $member;
        }

        /**
         * sending mail
         *
         * @return void
         */
        public function send()
        {
            $this->setView('dxl-memberships/src/admin/views/mails/profile-deactivated-mail.php');
            add_filter('wp_mail_content_type', [$this, 'setContentType']);
            $mail = wp_mail($this->email, $this->subject, file_get_contents($this->view), $this->headers, $this->attachments);
            remove_filter('wp_mail_content_type', [$this, 'setContentType']);
            
            return $mail;
        }
    }
}

?>