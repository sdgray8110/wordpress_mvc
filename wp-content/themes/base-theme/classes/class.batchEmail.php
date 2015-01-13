<?php

class batchEmail {
    static function send_emails($type, $user = null) {
        $data = new $type($user);
        $base_template = "email/baseTemplate";

        foreach($data->recipients as $recipient) {
            $recipient->content = markupHelper::render_template($recipient->template, $recipient);
            $recipient->message = markupHelper::render_template($base_template, $recipient);

            batchEmail::send_email($recipient);
        }

        return count($data->recipients);
    }

    static function send_email($recipient) {
        wp_mail( $recipient->user->email, $recipient->subject, $recipient->message, $recipient->headers);
    }
}

class emailModel {
    protected $subject, $users, $template, $ccRecipients, $membershipVP;
    public $recipients = array();

    protected function globals() {
        $this->membershipVP = SV_User::get_officer('Membership Chair');
        $this->sender = 'Siskiyou Velo Membership <membership@siskiyouvelo.org>';
        $this->debug = get_option('sv_debug');
        $this->ccRecipients = array(
            'gray8110@gmail.com',
            'membership@siskiyouvelo.org'
        );
    }

    protected function setup() {
        $extend = method_exists($this, 'emailExtensions');

        foreach($this->users as $user) {
            $pos = count($this->recipients);
            $item = (object) array(
                "template" => $this->template,
                "subject" => $this->subject,
                "user" => $user,
                "ccRecipients" => $this->ccRecipients,
                "membershipVP" => $this->membershipVP,
                "from" => "Siskiyou Velo Membership <membership@siskiyouvelo.org>"
            );

            if ($extend) {
                $this->emailExtensions($this->recipients[$pos]);
            }

            if ($this->debug) {
                $item->ccRecipients = array(
                    'webmaster@siskiyouvelo.org'
                );
            }

            $item->headers = $this->set_headers($item);
            $this->recipients[] = $item;
        }
    }

    private function set_headers($item) {
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: ' . $item->from . "\r\n";

        if (count($item->ccRecipients)) {
            $headers .= 'Cc: ' . implode(',', $item->ccRecipients) . "\r\n";
        }

        return $headers;
    }
}

// Provides Model for the provided User following a join
class joinThankYou extends emailModel {
    public function __construct($user) {
        $this->globals();
        $this->subject = "[Siskiyou Velo Membership] - Thank You For Joining Siskiyou Velo";
        $this->template = "email/join-thank-you";
        $this->users = array($user);
        $this->setup();
    }
}

// Provides Model for the provided User following a renewal
class renewalThankYou extends emailModel {
    public function __construct($user) {
        $this->globals();
        $this->subject = "[Siskiyou Velo Membership] - Thank You For Renewing With Siskiyou Velo";
        $this->template = "email/renewal-thank-you";
        $this->users = array($user);
        $this->setup();
    }
}

// Triggered by Daily Cron
// Provides Model for the users approaching expiration
class renewalReminder extends emailModel {
    public function __construct() {
        $this->globals();
        $this->subject = "[Siskiyou Velo Membership] - Your Renewal Date is Fast Approaching";
        $this->template = "email/renewal-reminder";
        $this->users = SV_User::get_users_for_renewal_reminder();
        $this->setup();
    }
}

// Triggered by Daily Cron
// Provides Model for the users approaching expiration
class expireReminder extends emailModel {
    public function __construct() {
        $this->globals();
        $this->subject = "[Siskiyou Velo Membership] - Your Renewal Date has Passed";
        $this->template = "email/expire-reminder";
        $this->users = SV_User::get_users_for_expired_reminder();
        $this->ccRecipients = array(
            'gray8110@gmail.com',
            'membership@siskiyouvelo.org'
        );
        $this->setup();
    }
}

// Provides Model for all newsletter recipients following publication of a newsletter
class newsletterNotification extends emailModel {
    private $newsletter;

    public function __construct() {
        $this->globals();
        $this->sender = 'Siskiyou Velo Newsletter <newsletter@siskiyouvelo.org>';
        $this->template = "email/newsletter-notification";
        $this->users = SV_User::get_newsletter_recipients();
        $this->ccRecipients = array();
        $this->from = "Siskiyou Velo Newsletters <newsletter@siskiyouvelo.org>";
        $this->getNewsletter();
        $this->setup();
    }

    private function getNewsletter() {
        $newsletter = Newsletter::get_most_recent_newsletter();
        $fmt = "The %s Siskiyou Velo Newsletter is now available";
        $this->subject = sprintf($fmt, $newsletter->issue);

        $this->newsletter = (object) array(
            "issue" => $newsletter->issue,
            "url" => get_permalink($newsletter->ID),
            "bulletpoints" => $newsletter->bulletpoints
        );
    }

    protected function emailExtensions($recipient) {
        $recipient->newsletter = $this->newsletter;
    }
}