<?php

namespace JetFire\Mailer;


class Mail {

    private static $mailer;
    private static $instance;

    public static function getInstance(){
        if(is_null(self::$instance))
            self::$instance = new self;
        return self::$instance;
    }

    public static function init(MailerInterface $mailer){
        self::$mailer = $mailer;
    }

    public function __call($name,$args){
        if(method_exists(self::$mailer,$name))
            call_user_func_array([self::$mailer,$name],$args);
        return self::getInstance();
    }

    public static function __callStatic($name,$args){
        if(method_exists(self::$mailer,$name))
            call_user_func_array([self::$mailer,$name],$args);
        return self::getInstance();
    }

} 