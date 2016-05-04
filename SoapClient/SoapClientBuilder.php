<?php

namespace InterInvest\AsponeBundle\SoapClient;

use BeSimple\SoapBundle\Soap\SoapClientBuilder as BaseSoapClientBuilder;
use BeSimple\SoapCommon\Helper;
use InterInvest\AsponeBundle\SoapClient\SoapClient;


class SoapClientBuilder extends BaseSoapClientBuilder
{
    /**
     * Finally returns a SoapClient instance.
     *
     * @return \BeSimple\SoapClient\SoapClient
     */
    public function build()
    {
        $this->validateOptions();

        return new SoapClient($this->wsdl, $this->getSoapOptions());
    }

    /**
     * SOAP attachment type MTOM.
     *
     * @return \BeSimple\SoapServer\SoapServerBuilder
     */
    public function withMtomAttachments()
    {
        $this->soapOptions['attachment_type'] = Helper::ATTACHMENTS_TYPE_MTOM;

        return $this;
    }

    /**
     * SOAP attachment type Base64.
     *
     * @return \BeSimple\SoapServer\SoapServerBuilder
     */
    public function withBase64Attachments()
    {
        $this->soapOptions['attachment_type'] = Helper::ATTACHMENTS_TYPE_BASE64;

        return $this;
    }

    /**
     * SOAP attachment type SwA.
     *
     * @return \BeSimple\SoapServer\SoapServerBuilder
     */
    public function withSwaAttachments()
    {
        $this->soapOptions['attachment_type'] = Helper::ATTACHMENTS_TYPE_SWA;

        return $this;
    }
}
