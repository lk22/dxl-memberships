<?php 
namespace DxlMembership\Classes\Mails;

use Dxl\Classes\Abstracts\AbstractMailer as Mailer;

if( !class_exists('ProfileActivated') )
{
    class ProfileActivated extends Mailer {

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
            $this->setView('dxl-memberships/src/admin/views/mails/profile-activated-mail.php');
            add_filter('wp_mail_content_type', [$this, 'setContentType']);
            $send = wp_mail($this->email, $this->getSubject(), file_get_contents($this->view), $this->getHeaders(), $this->getAttachments());
            remove_filter('wp_mail_content_type', [$this, 'setContentType']);
        
            return $send;
        }
    }
}

?>