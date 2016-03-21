<?php

namespace JetFire\Mailer;


/**
 * Interface MailerInterface
 * @package JetFire\Mailer
 */
interface MailerInterface {

    /**
     * @param $config
     */
    public function __construct($config);

    /**
     * @param null $to
     * @param null $from
     * @param null $subject
     * @param null $content
     * @param null $file
     * @return mixed
     */
    public function send($to = null,$from = null,$subject = null,$content = null,$file = null);

    /**
     * @param $subject
     * @return mixed
     */
    public function subject($subject);

    /**
     * @return mixed
     */
    public function from();

    /**
     * @return mixed
     */
    public function to();

    /**
     * @return mixed
     */
    public function addTo();

    /**
     * @return mixed
     */
    public function cc();

    /**
     * @return mixed
     */
    public function addCc();

    /**
     * @return mixed
     */
    public function bcc();

    /**
     * @return mixed
     */
    public function addBcc();

    /**
     * @param $content
     * @return mixed
     */
    public function content($content);

    /**
     * @param $html
     * @return mixed
     */
    public function html($html);

    /**
     * @param $file
     * @param null $name
     * @return mixed
     */
    public function file($file,$name = null);

    /**
     * @return mixed
     */
    public function getMail();

}