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

    public function send($subject = null, $from = null, $to = null, $content = null, $file = null)
    {
        if(is_null($to)) {
            Swift_Mailer::newInstance(call_user_func($this->getTransport()))->send($this->message);
            return true;
        }
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