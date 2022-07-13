<?php 
    namespace DxlMembership\Classes\Mail;

    use Dxl\Classes\Abstracts\AbstractMailer as Mailer;

    if ( !defined('ABSPATH') ) {
        exit;
    }

    if( ! class_exists('MembershipCanceled') ) {
        class MembershipCanceled extends Mailer {

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

            /**
             * Mail template definition
             *
             * @return void
             */
            protected function template() 
            {
                $template = "<h2>Kære " . $this->member->name . "</h2>\n\n";
                $template .= "<p>Vi er kede af at måtte meddele at vi har været nødsaget til at anullere dit medlemsskab.</p>"
                $template .= "<p>Dette skyldes mangel på autofornyelse</p>\n";
                $template .= "<p>Skulle du alligevel ønske og fortsætte dit medlemsskab, bedes du kontakte bestyrrelsen</p>\n";

                return $template;
            }
        }
    }
?>