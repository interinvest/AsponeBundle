<?php

namespace  InterInvest\AsponeBundle\SoapClient;

class WsseAuth {
    private $Username;
    private $Password;
    private $Nonce;
    private $Created;
    function __construct($username, $password, $nonce, $created) {
        $this->Username=$username;
        $this->Password = $password;
        $this->Nonce = $nonce;
        $this->Created = $created;
        $this->Password->enc_value = base64_encode(sha1($nonce->enc_value . $created->enc_value . $password->enc_value));
    }
}