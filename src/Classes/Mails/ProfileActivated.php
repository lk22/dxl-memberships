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
            add_filter('wp_mail_content_type', [$this, 'setContentType']);
            $send = wp_mail($this->email, $this->getSubject(), $this->template(), $this->getHeaders(), $this->getAttachments());
            remove_filter('wp_mail_content_type', [$this, 'setContentType']);
        
            return $send;
        }

        /**
         * mail template definition
         */
        protected function template() 
        {
            $template = "<h2>Kære " . $this->member->name . "</h2>";
            $template .= "<p>Din profil er nu aktiveret, din profil giver dig en masse fordele.</p>";
            $template .= "<ul>";
            $template .= "<li>Du får dit helt eget administrations panel</li>";
            $template .= "<li>hvor du kan tilføje events og andre aktiviteter som alle andre medlemmer kan deltage i.</li>";
            $template .= "<li>Admininistrere dine egne bruger oplysninger såfremt dine data skulle ændres</li>";
            $template .= "</ul>";
            $template .= "<p>Du kan logge ind på din profil her: <a href='" . get_site_url() . "/profil/" . $this->member->id . "'>" . get_site_url() . "/profil/" . $this->member->id . "</a></p>";

            return $template;
        }
    }
}

?>