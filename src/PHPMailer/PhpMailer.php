<?php

namespace JetFire\Mailer\PhpMailer;


use JetFire\Mailer\MailerInterface;

/**
 * Class PhpMailer
 * @package JetFire\Mailer\PhpMailer
 */
class PhpMailer implements MailerInterface{

    /**
     * @var \PHPMailer\PHPMailer\PHPMailer
     */
    private $mail;

    /**
     * @var array
     */
    private $config = [
        //smtp config
        'transport' => 'smtp',
        'port' => 25,
        'user' => 'user',
        'pass' => 'pass',
        //sendmail config
        'command' => '/usr/sbin/sendmail -bs',
        'charset' => 'UTF-8',

    ];

    /**
     * @param $config
     */
    public function __construct($config){
        $this->config = array_merge($this->config,$config);
        $this->mail = new \PHPMailer\PHPMailer\PHPMailer();
    }

    /**
     * @return \PHPMailer\PHPMailer\PHPMailer
     */
    public function getMail(){
        return $this->mail;
    }

    /**
     * @param null $to
     * @param null $from
     * @param null $subject
     * @param null $content
     * @param null $file
     * @return bool
     */
    public function send($to = null, $from = null, $subject = null, $content = null, $file = null)
    {
        if(is_null($to)){
            $this->config();
            if($this->mail->send()){
                $this->mail = new \PHPMailer\PHPMailer\PHPMailer();
                return true;
            }
            return false;
        }elseif(!is_null($to) && !is_null($from) && !is_null($subject) && !is_null($content)) {
            $this->subject($subject);
            $this->from($from);
            $this->to($to);
            $this->content($content);
            if (!is_null($file)) $this->file($file);
            $this->mail->send();
            $this->mail = new \PHPMailer\PHPMailer\PHPMailer();
            return true;
        }
        return false;
    }

    /**
     *
     */
    private function config(){
        if(isset($this->config['lang']['local']) && isset($this->config['lang']['path']))
            $this->mail->setLanguage($this->config['lang']['local'], $this->config['lang']['path']);
        $this->mail->CharSet = $this->config['charset'];
        switch($this->config['transport']){
            case 'smtp':
                $this->mail->isSMTP();
                if(isset($this->config['debug'])) {
                    $this->mail->SMTPDebug = $this->config['debug'];
                    $this->mail->Debugoutput = 'html';
                }
                $this->mail->Host = $this->config['host'];
                if(isset($this->config['user'])) {
                    $this->mail->SMTPAuth = true;
                    $this->mail->Username = $this->config['user'];
                    $this->mail->Password = $this->config['pass'];
                    $this->mail->Port = $this->config['port'];
                    if(isset($this->config['encrypt'])) $this->mail->SMTPSecure = $this->config['encrypt'];
                }
                break;
            case 'sendmail':
                $this->mail->isSendmail();
                $this->mail->Sendmail = $this->config['command'];
                break;
        }
    }

    /**
     * @return $this
     */
    public function from()
    {
        $args = func_get_args();
        if(isset($args[1]))
             $this->mail->setFrom($args[0],$args[1]);
        else {
            if(is_array($args[0]))
                foreach($args[0] as $name => $address)
                    $this->mail->setFrom($name,$address);
        }
        return $this;
    }

    /**
     * @param $subject
     * @return $this
     */
    public function subject($subject)
    {
        $this->mail->Subject = $subject;
        return $this;
    }

    /**
     * @return $this
     */
    public function to()
    {
        $args = func_get_args();
        if(isset($args[1]))
            $this->mail->addAddress($args[0],$args[1]);
        else{
            if(is_array($args[0])) {
                foreach($args[0] as $name => $address)
                    $this->mail->addAddress($address,$name);
            }else
                $this->mail->addAddress($args[0]);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function addTo()
    {
        $args = func_get_args();
        isset($args[1])
            ? $this->mail->addAddress($args[0],$args[1])
            : $this->mail->addAddress($args[0]);
        return $this;
    }

    /**
     * @return $this
     */
    public function cc()
    {
        $args = func_get_args();
        isset($args[1])
            ? $this->mail->addCC($args[0],$args[1])
            : $this->mail->addCC($args[0]);
        return $this;
    }

    /**
     * @return $this
     */
    public function addCc()
    {
        $args = func_get_args();
        isset($args[1])
            ? $this->mail->addCC($args[0],$args[1])
            : $this->mail->addCC($args[0]);
        return $this;
    }

    /**
     * @return $this
     */
    public function bcc()
    {
        $args = func_get_args();
        isset($args[1])
            ? $this->mail->addBCC($args[0],$args[1])
            : $this->mail->addBCC($args[0]);
        return $this;
    }

    /**
     * @return $this
     */
    public function addBcc()
    {
        $args = func_get_args();
        isset($args[1])
            ? $this->mail->addBCC($args[0],$args[1])
            : $this->mail->addBCC($args[0]);
        return $this;
    }

    /**
     * @param $content
     * @return $this
     */
    public function content($content)
    {
        $this->mail->Body = $content;
        return $this;
    }

    /**
     * @param $html
     * @return $this
     */
    public function html($html)
    {
        $this->mail->isHTML(true);
        $this->mail->Body = $html;
        return $this;
    }

    /**
     * @param $content
     * @return $this
     */
    public function addPart($content){
        $this->mail->AltBody = $content;
        return $this;
    }

    /**
     * @param $file
     * @param null $name
     * @return $this
     */
    public function file($file, $name = null)
    {
        is_null($name)
            ? $this->mail->addAttachment($file)
            : $this->mail->addAttachment($file,$name);
        return $this;
    }
}