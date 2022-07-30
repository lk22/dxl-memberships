<?php 
namespace DxlMembership\Classes\Mails;

use Dxl\Classes\Abstracts\AbstractMailer as Mailer;

if ( !class_exists('AcceptPayment') ) {
    class AcceptPayment extends Mailer {

        /**
         * Member data
         *
         * @var \Dxl\Classes\Member
         */
        public $member;

        /**
         * Constructor
         *
         * @param \Dxl\Classes\Member $member
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

        /**
         * Mail template definition
         *
         * @return void
         */
        protected function template() 
        {
            $template = "<h2>Kære " . $this->member->name . "</h2>\n\n";
            $template .= "<p>Du er nu registreret som medlem</p>\n";
            $template .= "<p>Vi byder dig velkommen i foreningen</p>\n";
            $template .= "<p>Hos medlem hos Danish Xbox League, får du adgang til en masse goder.</p>";
            $template .= "<ul>";
            $template .= "<li>Adgang til 2 LAN begivenheder om året i følgende måneder(April, Oktober)</li>";
            $template .= "<li>Vi giver dig også adgang til dit helt eget administrations værktøj.</li>";
            $template .= "<li>Et godt fælleskab, bestående af andre passioneret xbox gamere.</li>";
            $template .= "<li>Adgang til vores Members Lounge på facebook</li>";
            $template .= "</ul>";

            $template .= "<p>Du kan se video tutorials her: <a href='" . get_site_url() . "/video-tutorials'>" . get_site_url() . "/video-tutorials</a></p>";
            $template .= "<p>Du kan logge ind på din profil her: <a href='" . get_site_url() . "/profil/" . $this->member->id . "'>" . get_site_url() . "/profil/" . $this->member->id . "</a></p>";
            
            return $template;
        }
    }
}
?>