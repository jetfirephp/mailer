<?php

namespace JetFire\Mailer\SwiftMailer;


use JetFire\Mailer\MailerInterface;
use Swift_Attachment;
use Swift_Mailer;
use Swift_MailTransport;
use Swift_Message;
use Swift_Preferences;
use Swift_SendmailTransport;
use Swift_SmtpTransport;

/**
 * Class SwiftMailer
 * @package JetFire\Mailer\SwiftMailer
 */
class SwiftMailer implements MailerInterface{

    /**
     * @var Swift_Message
     */
    private $message;
    /**
     * @var Swift_Mailer
     */
    private $mailer;
    /**
     * @var array
     */
    private $config = [
        //smtp config
        'transport' => 'smtp',
        'port' => 25,
        'user' => '',
        'pass' => '',
        //sendmail config
        'command' => '/usr/sbin/sendmail -bs',
        'charset' => 'UTF-8',
    ];

    /**
     * @param $config
     */
    public function __construct($config){
        $this->config = array_merge($this->config,$config);
        $this->mailer = Swift_Mailer::newInstance(call_user_func($this->getTransport()));
        Swift_Preferences::getInstance()->setCharset($this->config['charset']);
    }

    /**
     * @return callable
     */
    private function getTransport(){
        switch($this->config['transport']){
            case 'smtp' :
                return function(){
                    $transport = (isset($this->config['encrypt']))
                        ? Swift_SmtpTransport::newInstance($this->config['host'], $this->config['port'],$this->config['encrypt'])
                        : Swift_SmtpTransport::newInstance($this->config['host'], $this->config['port']);
                    if(isset($this->config['local']))
                        $transport->setLocalDomain($this->config['local']);
                    return $transport->setUsername($this->config['user'])
                        ->setPassword($this->config['pass']);
                };
                break;
            case 'mail' :
                return function(){
                    return Swift_MailTransport::newInstance();
                };
                break;
            case 'sendmail' :
                return function(){
                    return Swift_SendmailTransport::newInstance($this->config['command']);
                };
                break;
        }
        return function(){
            return Swift_MailTransport::newInstance();
        };
    }

    /**
     * @return mixed
     */
    public function getMail(){
        if(is_null($this->message))
            $this->message = Swift_Message::newInstance();
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getMailer(){
        return $this->mailer;
    }

    /**
     * @param null $to
     * @param null $from
     * @param null $subject
     * @param null $content
     * @param null $file
     * @return bool
     */
    public function send($to = null,$from = null,$subject = null,$content = null, $file = null)
    {
        if(is_null($to)) {
            $this->mailer->send($this->message);
            $this->message = null;
            return true;
        }elseif(!is_null($to) && !is_null($from) && !is_null($subject) && !is_null($content)) {
            $this->subject($subject);
            $this->from($from);
            $this->to($to);
            $this->content($content);
            if (!is_null($file)) $this->file($file);
            $this->mailer->send($this->message);
            $this->message = null;
            return true;
        }
        return false;
    }

    /**
     * @param $subject
     * @return $this
     */
    public function subject($subject)
    {
        $this->getMail()->setSubject($subject);
        return $this;
    }

    /**
     * @return $this
     */
    public function from()
    {
        $args = func_get_args();
        (func_num_args() == 2)
            ? $this->getMail()->setFrom([$args[0] => $args[1]])
            : $this->getMail()->setFrom($args[0]);
        return $this;
    }

    /**
     * @return $this
     */
    public function to()
    {
        $args = func_get_args();
        (func_num_args() == 2)
            ? $this->getMail()->setTo([$args[0] => $args[1]])
            : $this->getMail()->setTo($args[0]);
        return $this;
    }

    /**
     * @return $this
     */
    public function addTo()
    {
        $args = func_get_args();
        (func_num_args() == 2)
            ? $this->message->addTo($args[0],$args[1])
            : $this->message->addTo($args[0]);
        return $this;
    }

    /**
     * @return $this
     */
    public function cc()
    {
        $args = func_get_args();
        (func_num_args() == 2)
            ? $this->message->setCc([$args[0] => $args[1]])
            : $this->message->setCc($args[0]);
        return $this;
    }

    /**
     * @return $this
     */
    public function addCc()
    {
        $args = func_get_args();
        (func_num_args() == 2)
            ? $this->message->addCc($args[0],$args[1])
            : $this->message->addCc($args[0]);
        return $this;
    }

    /**
     * @return $this
     */
    public function bcc()
    {
        $args = func_get_args();
        (func_num_args() == 2)
            ? $this->message->setBcc([$args[0] => $args[1]])
            : $this->message->setBcc($args[0]);
        return $this;
    }

    /**
     * @return $this
     */
    public function addBcc()
    {
        $args = func_get_args();
        (func_num_args() == 2)
            ? $this->message->addBcc($args[0],$args[1])
            : $this->message->addBcc($args[0]);
        return $this;
    }

    /**
     * @param $content
     * @return $this
     */
    public function content($content)
    {
        $this->message->setBody($content, 'text/plain');
        return $this;
    }

    /**
     * @param $content
     * @param $type
     * @return $this
     */
    public function addPart($content,$type){
        $this->message->addPart($content,$type);
        return $this;
    }

    /**
     * @param $html
     * @return $this
     */
    public function html($html)
    {
        $this->message->setBody($html, 'text/html');
        return $this;
    }

    /**
     * @param $file
     * @param null $name
     * @return $this
     */
    public function file($file,$name = null)
    {
        $attachment = (is_null($name))
            ? Swift_Attachment::fromPath($file)
            : Swift_Attachment::fromPath($file)->setFilename($name);;
        $this->message->attach($attachment);
        return $this;
    }
}