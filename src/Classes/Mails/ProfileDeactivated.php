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
            add_filter('wp_mail_content_type', [$this, 'setContentType']);
            $mail = wp_mail($this->email, $this->subject, $this->template(), $this->headers, $this->attachments);
            remove_filter('wp_mail_content_type', [$this, 'setContentType']);
            
            return $mail;
        }

        protected function template() 
        {
            $template = "<h2>Kære " . $this->member->name . "</h2>\n\n";
            $template .= "<p>Vi har deaktiveret din profil pga mangel på medlemsskab</p>\n";
            $template .= "<p>Skulle du ønske og fortsætte med at bruge din administrations profil</p>\n";
            $template .= "<p>Vil vi bede om at ansøge om et nyt medlemsskab</p>\n";

            return $template;
        }
    }
}

?>