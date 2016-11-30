<?php

namespace JetFire\Mailer;


/**
 * Class Mail
 * @package JetFire\Mailer
 * @method object to(...$value)
 * @method \PHPMailer\PHPMailer\PHPMailer|mixed getMail()
 */
class Mail
{

    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var
     */
    private static $instance;

    /**
     * Mail constructor.
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        self::$instance = $this;
        $this->mailer = $mailer;
    }

    /**
     * @return Mail
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * @param $name
     * @param $arguments
     * @return MailerInterface
     */
    public function __call($name, $arguments)
    {
        return (method_exists($this->mailer, $name))
            ? call_user_func_array([$this->mailer, $name], $arguments)
            : call_user_func_array([$this->mailer->getMail(), $name], $arguments);
    }

    /**
     * @param $name
     * @param $arguments
     * @return MailerInterface
     */
    public static function __callStatic($name, $arguments)
    {
        return (method_exists(self::getInstance()->mailer, $name))
            ? call_user_func_array([self::getInstance()->mailer, $name], $arguments)
            : call_user_func_array([self::getInstance()->mailer->getMail(), $name], $arguments);
    }

} 