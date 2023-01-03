<?php 
namespace DxlMembership\Classes\Mails;

use Dxl\Classes\Abstracts\AbstractMailer as Mailer;

if ( !class_exists('MemberRequestReceipt') ) {
    class MemberRequestReceipt extends Mailer {

        /**
         * Member data
         **/
        public $member;

        /**
         * Membership data
         **/
        public $membership;

        /**
         * Constructor
         */
        public function __construct($member, $membership)
        {
            $this->member = $member;
            $this->membership = $membership;
        }

        /**
         * sending mail
         *
         * @return void
         */
        public function send()
        {
            add_filter('wp_mail_content_type', [$this, 'setContentType']);
            $mail = wp_mail($this->receiver, $this->subject, $this->template(), $this->headers, $this->attachments);
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
            $template .= "<p>Vi byder dig velkommen i foreningen</p>\n";
            $template .= "Vi har modtaget din forespørge om medlemskab.\n";

            $template .= "<p>Hos medlem hos Danish Xbox League, får du adgang til en masse goder.</p>";
            $template .= "<ul>";
            $template .= "<li>Adgang til 2 LAN begivenheder om året i følgende måneder(April, Oktober)</li>";
            $template .= "<li>Vi giver dig også adgang til dit helt eget administrations værktøj.</li>";
            $template .= "<li>Et godt fælleskab, bestående af andre passioneret xbox gamere.</li>";
            $template .= "<li>Adgang til vores Members Lounge på facebook</li>";
            $template .= "</ul>";

            $template .= "Vi har modtaget følgende oplysninger: \n";
            $template .= "<ul>";
            $template .= "<li>Navn: " . $this->member->name . "</li>";
            $template .= "<li>Gamertag: " . $this->member->gamertag . "</li>";
            $template .= "<li>Email: " . $this->member->email . "</li>";
            $template .= "<li>Telefon: " . $this->member->phone . "</li>";
            $template .= "<li>Adresse: " . $this->member->address . "</li>";
            $template .= "<li>Postnummer: " . $this->member->zipcode . "</li>";
            $template .= "<li>By: " . $this->member->city . "</li>";
            $template .= "<li>Kommune: " . $this->member->municipality . "</li>";
            $template .= "<li>Fødselsdag: " . $this->member->birthyear . "</li>";
            $template .= "<li>Medlemskabstype: " . $this->membership->name . "</li>";
            $template .= "</ul>";
            $template .= "Du vil få tilsendt en faktura på dit medlemskab snarest muligt. \n";

            $template .= "Hvis disse oplysninger ikke er korrekte bedes du kontakte os for at få rettet dem.\n\n";
            
            $template .= "Med venlig hilsen\n";
            $template .= "Danish Xbox League\n";
            return $template;
        }
    }
}
?>