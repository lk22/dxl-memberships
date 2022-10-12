<?php 
namespace DxlMembership\Classes\Mails;

use Dxl\Classes\Abstracts\AbstractMailer as Mailer;

if( !class_exists('MemberUserPasswordReset') )
{
    class MemberUserPasswordReset extends Mailer {

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
            $send = wp_mail($this->receiver, $this->getSubject(), $this->template(), $this->getHeaders(), $this->getAttachments());
            remove_filter('wp_mail_content_type', [$this, 'setContentType']);
        
            return $send;
        }

        /**
         * mail template definition
         */
        protected function template() 
        {
            $template = "<h2>Kære " . $this->member->name . "</h2>";
            $template .= "<p>Vi har nulstillet din adgangskode.</p>";
            $template .= "<p>Dit nye login er følgende:</p>";
            $template .= "<p>" . $this->member->gamertag . "</p>";
            $template .= "<p>" . $this->member->gamertag . "</p>";
            $template .= "<p>Du kan logge ind på din profil her: <a href='" . get_site_url() . "/wp-login.php" . "'</a></p>";

            return $template;
        }
    }
}

?>