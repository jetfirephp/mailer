<?php

namespace JetFire\Mailer;


interface MailerInterface {

    public function __construct($config);

    public function send($to = null,$from = null,$subject = null,$content = null,$file = null);

    public function subject($subject);

    public function from();

    public function to();

    public function addTo();

    public function cc();

    public function addCc();

    public function bcc();

    public function addBcc();

    public function content($content);

    public function html($html);

    public function file($file,$name = null);



} 