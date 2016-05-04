<?php

namespace InterInvest\AsponeBundle\SoapClient;

use BeSimple\SoapClient\XmlMimeFilter as BaseXmlMimeFilter;
use BeSimple\SoapCommon\FilterHelper;
use BeSimple\SoapCommon\Helper;
use BeSimple\SoapCommon\SoapRequest;
use BeSimple\SoapCommon\SoapRequestFilter;


class XmlMimeFilter extends BaseXmlMimeFilter
{
    /**
     * Modify the given request XML.
     *
     * @param \BeSimple\SoapCommon\SoapRequest $request SOAP request
     *
     * @return void
     */
    public function filterRequest(SoapRequest $request)
    {
        // get \DOMDocument from SOAP request
        $dom = $request->getContentDocument();

        // create FilterHelper
        $filterHelper = new FilterHelper($dom);

        // add the neccessary namespaces
        $filterHelper->addNamespace('mime', Helper::NS_XMLMIME);

        // get xsd:base64binary elements
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('XOP', Helper::NS_XOP);
        $query = '//XOP:Include/..';
        $nodes = $xpath->query($query);

        // exchange attributes
        if ($nodes->length > 0) {
            foreach ($nodes as $node) {
                if ($node->hasAttribute('contentType')) {
                    $contentType = $node->getAttribute('contentType');
                    $node->removeAttribute('contentType');
                    $filterHelper->setAttribute($node, Helper::NS_XMLMIME, 'contentType', $contentType);
                }
            }
        }

    }
}
