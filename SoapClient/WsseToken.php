<?php

namespace InterInvest\AsponeBundle\SoapClient;

class WsseToken {
    private $UsernameToken;
    function __construct ($innerVal){
        $this->UsernameToken = $innerVal;
    }
}