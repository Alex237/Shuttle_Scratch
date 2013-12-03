<?php

require_once './vendor/Swiftmailer/swift_required.php';

/**
 * Mailer description
 * 
 * <p>The mailer core<p>
 * 
 * @author Fabien MORCHOISNE <f.morchoisne@insta.fr>
 */
class Mailer
{

    /**
     * The swiftmailer transport layer
     * 
     * @var \Swift_SmtpTransport 
     */
    private $transport;

    /**
     * Construct
     * 
     */
    public function __construct() {
        $transport = Swift_SmtpTransport::newInstance("smtp.gmail.com", 465, 'ssl');
        $transport->setUsername("shuttleticket@gmail.com");
        $transport->setPassword("Insta2013");
        $this->transport = $transport;
    }

    /**
     * Send the signup confirmation mail
     * 
     * @param array $user The userdatas
     * @param string $token The user activation token
     * @param \Twig_Environment $twig The twig environment
     */
    public function mailUserRegister($user, \Twig_Environment $twig) {

        $message = Swift_Message::newInstance();
        $message->setTo($user['email']);
        $message->setSubject('Shuttle - Confirmez votre inscription');
        $message->setBody($twig->render('mail/userRegister.html.twig', array(
                    'email' => $user['email'],
                    'token' => $user['token']
                )), 'text/html');
        $message->setFrom("noreply@shuttle.dev", "Shuttle");

        $mailer = Swift_Mailer::newInstance($this->transport);
        $mailer->send($message);
    }

    /**
     * Send the user creation validation mail
     * 
     * @param array $user The userdatas
     * @param \Twig_Environment $twig The twig environment
     */
    public function mailUserCreate($user, \Twig_Environment $twig) {

        $message = Swift_Message::newInstance();
        $message->setTo($user['email']);
        $message->setSubject('Shuttle - Confirmez votre inscription');
        $message->setBody($twig->render('mail/userCreate.html.twig', array(
                    'email' => $user['email'],
                    'token' => $user['token'],
                    'password' => $user['password']
                )), 'text/html');
        $message->setFrom("noreply@shuttle.dev", "Shuttle");

        $mailer = Swift_Mailer::newInstance($this->transport);
        $mailer->send($message);
    }

}