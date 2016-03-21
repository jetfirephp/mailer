<?php

namespace JetFire\Mailer;


/**
 * Class Mail
 * @package JetFire\Mailer
 */
class Mail {

    /**
     * @var \JetFire\Mailer\PHPMailer\PhpMailer | \JetFire\Mailer\SwiftMailer\SwiftMailer
     */
    private static $mailer;
    /**
     * @var
     */
    private static $instance;

    /**
     * @return Mail
     */
    public static function getInstance(){
        if(is_null(self::$instance))
            self::$instance = new self;
        return self::$instance;
    }

    /**
     * @param MailerInterface $mailer
     */
    public static function init(MailerInterface $mailer){
        self::$mailer = $mailer;
    }

    /**
     * @param $name
     * @param $args
     * @return mixed
     */
    public function __call($name,$args){
        return (method_exists(self::$mailer,$name))
            ? call_user_func_array([self::$mailer,$name],$args)
            : call_user_func_array([self::$mailer->getMail(),$name],$args);
    }

    /**
     * @param $name
     * @param $args
     * @return mixed
     */
    public static function __callStatic($name,$args){
        return (method_exists(self::$mailer,$name))
            ? call_user_func_array([self::$mailer,$name],$args)
            : call_user_func_array([self::$mailer->getMail(),$name],$args);
    }

} 