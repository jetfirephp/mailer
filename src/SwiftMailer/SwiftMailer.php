<?php

namespace JetFire\Mailer\SwiftMailer;


use JetFire\Mailer\MailerInterface;
use Swift_Attachment;
use Swift_Mailer;
use Swift_MailTransport;
use Swift_Message;
use Swift_SendmailTransport;
use Swift_SmtpTransport;

class SwiftMailer implements MailerInterface{

    private $message;
    private $mailer;
    private $config = [
        //smtp config
        'transport' => 'smtp',
        'port' => 25,
        'user' => '',
        'pass' => '',
        //sendmail config
        'command' => '/usr/sbin/sendmail -bs'
    ];

    public function __construct($config){
        $this->config = array_merge($this->config,$config);
        $this->mailer = Swift_Mailer::newInstance(call_user_func($this->getTransport()));
    }

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
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getMailer(){
        return $this->mailer;
    }

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

    public function subject($subject)
    {
        if(is_null($this->message))
            $this->message = Swift_Message::newInstance();
        else
            $this->message->setSubject($subject);
    }

    public function from()
    {
        if(is_null($this->message))
            $this->message = Swift_Message::newInstance();
        $args = func_get_args();
        (func_num_args() == 2)
            ? $this->message->setFrom($args[0],$args[1])
            : $this->message->setFrom($args);
    }

    public function to()
    {
        if(is_null($this->message))
            $this->message = Swift_Message::newInstance();
        $args = func_get_args();
        (func_num_args() == 1)
            ? $this->message->setFrom($args[0])
            : $this->message->setFrom($args);
    }

    public function addTo()
    {
        $args = func_get_args();
        (func_num_args() == 2)
            ? $this->message->addTo($args[0],$args[1])
            : $this->message->addTo($args);
    }

    public function cc()
    {
        $args = func_get_args();
        (func_num_args() == 1)
            ? $this->message->setCc($args[0])
            : $this->message->setCc($args);
    }

    public function addCc()
    {
        $args = func_get_args();
        (func_num_args() == 2)
            ? $this->message->addCc($args[0],$args[1])
            : $this->message->addCc($args);
    }

    public function bcc()
    {
        $args = func_get_args();
        (func_num_args() == 1)
            ? $this->message->setBcc($args[0])
            : $this->message->setBcc($args);
    }

    public function addBcc()
    {
        $args = func_get_args();
        (func_num_args() == 2)
            ? $this->message->addBcc($args[0],$args[1])
            : $this->message->addBcc($args);
    }

    public function content($content)
    {
        $this->message->setBody($content, 'text/plain');
    }

    public function addPart($content,$type){
        $this->message->addPart($content,$type);
    }

    public function html($html)
    {
        $this->message->setBody($html, 'text/html');
    }

    public function file($file,$name = null)
    {
        $attachment = (is_null($name))
            ? Swift_Attachment::fromPath($file)
            : Swift_Attachment::fromPath($file)->setFilename($name);;
        $this->message->attach($attachment);
    }
}