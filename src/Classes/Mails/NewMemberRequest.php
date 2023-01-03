<?php 
namespace DxlMembership\Classes\Mails;

use Dxl\Classes\Abstracts\AbstractMailer as Mailer;

if ( !class_exists('NewMemberRequest') ) {
    class NewMemberRequest extends Mailer {

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
            $template = "<h2>Nyt medlemsskab</h2>\n\n"; 
            $template .= "<p>Der er blevet registreret et nyt afventende medlemsskab</p>\n";
            $template .= "<p>Følgende oplysninger er registreret: </p>\n";
            $template .= "<ul>";
            $template .= "<li>Navn: " . $this->member->name . "<li>";
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
            return $template;
        }
    }
}
?>