<?php


namespace InterInvest\AsponeBundle\SoapClient;

use BeSimple\SoapCommon\Converter\SwaTypeConverter;
use BeSimple\SoapClient\Curl;
use BeSimple\SoapClient\SoapKernel;
use BeSimple\SoapClient\SoapRequest;
use BeSimple\SoapClient\SoapResponse;
use BeSimple\SoapClient\WsdlDownloader;
use BeSimple\SoapCommon\Converter\MtomTypeConverter;
use BeSimple\SoapCommon\Helper;
use \SoapVar;
use \SoapHeader;


class SoapClient extends \SoapClient
{
    /**
     * Soap version.
     *
     * @var int
     */
    protected $soapVersion = SOAP_1_1;

    /**
     * Tracing enabled?
     *
     * @var boolean
     */
    protected $tracingEnabled = true;

    /**
     * cURL instance.
     *
     * @var \BeSimple\SoapClient\Curl
     */
    protected $curl = null;

    /**
     * @var SoapKernel|null
     */
    protected $soapKernel = null;

    /**
     * Last request headers.
     *
     * @var string
     */
    private $lastRequestHeaders = '';

    /**
     * Last request.
     *
     * @var string
     */
    private $lastRequest = '';

    /**
     * Last response headers.
     *
     * @var string
     */
    private $lastResponseHeaders = '';

    /**
     * Last response.
     *
     * @var string
     */
    private $lastResponse = '';

    /**
     * Login service.
     *
     * @var string
     */
    protected $username = '';

    /**
     * Password service.
     *
     * @var string
     */
    protected $password = '';

    /**
     * Login context.
     *
     * @var string
     */
    protected $contextLogin = '';

    /**
     * Password context.
     *
     * @var string
     */
    protected $contextPassword = '';

    /**
     * Service Version.
     *
     * @var string
     */
    protected $serviceVersion = '';

    /**
     * Service Url.
     *
     * @var string
     */
    protected $service = '';

    /**
     * Options.
     *
     * @var array
     */
    protected $options = array();

    /**
     * xmlns web
     *
     * @var string
     */
    public static $strContext = "";

    /**
     * @var bool
     */
    protected $withMtom = false;


    /**
     * Constructor.
     *
     * @param string $wsdl WSDL file
     * @param array(string=>mixed) $options Options array
     */
    public function __construct($wsdl, array $options = array())
    {
        // store SOAP version
        if (isset($options['soap_version'])) {
            $this->soapVersion = $options['soap_version'];
        }

        $this->curl = new Curl($options);

        if (isset($options['extra_options'])) {
            unset($options['extra_options']);
        }

        $wsdlFile = $this->loadWsdl($wsdl, $options);

        $this->soapKernel = new SoapKernel();
        // set up type converter and mime filter
        $this->configureMime($options);
        // we want the exceptions option to be set
        $options['exceptions'] = false;

        // disable WSDL caching as we handle WSDL caching for remote URLs ourself
        $options['cache_wsdl'] = WSDL_CACHE_NONE;

        $this->options = $options;
        parent::__construct($wsdlFile, $options);
    }

    /**
     * Get last response HTTP body.
     *
     * @return string
     */
    public function __getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Configure filter and type converter for SwA/MTOM.
     *
     * @param array &$options SOAP constructor options array.
     *
     * @return void
     */
    private function configureMime(array &$options)
    {
        if (isset($options['attachment_type']) && Helper::ATTACHMENTS_TYPE_BASE64 !== $options['attachment_type']) {
            // register mime filter in SoapKernel
            $MimeFilter = new MimeFilter($options['attachment_type']);
            $this->soapKernel->registerFilter($MimeFilter);
            // configure type converter
            if (Helper::ATTACHMENTS_TYPE_SWA === $options['attachment_type']) {
                $converter = new SwaTypeConverter();
                $converter->setKernel($this->soapKernel);
            } elseif (Helper::ATTACHMENTS_TYPE_MTOM === $options['attachment_type']) {
                $IIXmlMimeFilter = new XmlMimeFilter($options['attachment_type']);
                $this->soapKernel->registerFilter($IIXmlMimeFilter);
                $converter = new MtomTypeConverter();
                $converter->setKernel($this->soapKernel);
            }
            // configure typemap
            if (!isset($options['typemap'])) {
                $options['typemap'] = array();
            }
            $options['typemap'][] = array(
                'type_name' => $converter->getTypeName(),
                'type_ns' => $converter->getTypeNamespace(),
                'from_xml' => function ($input) use ($converter) {
                    return $converter->convertXmlToPhp($input);
                },
                'to_xml' => function ($input) use ($converter) {
                    return $converter->convertPhpToXml($input);
                },
            );
        }
    }

    /**
     * @param $userName
     */
    public function setUsername($userName)
    {
        $this->username = $userName;
    }

    /**
     * @param $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @param $contextLogin
     */
    public function setContextLogin($contextLogin)
    {
        $this->contextLogin = $contextLogin;
    }

    /**
     * @param $contextPassword
     */
    public function setContextPassword($contextPassword)
    {
        $this->contextPassword = $contextPassword;
    }

    /**
     * @param $context
     */
    public function setContext($context)
    {
        self::$strContext = $context;
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return self::$strContext;
    }

    /**
     * @param $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }

    /**
     * @param $serviceVersion
     */
    public function setServiceVersion($serviceVersion)
    {
        $this->serviceVersion = $serviceVersion;
    }

    /**
     * Set les headers
     *
     */
    public function setSoapHeaders()
    {
        $nonce = time();
        $created = date('Y-m-dTH:i:s');

        $strWSSENS = "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd";
        $strContext = self::$strContext;
        $strService = $this->service;

        $objSoapVarUser = new SoapVar($this->username, XSD_STRING, NULL, $strWSSENS, NULL, $strWSSENS);
        $objSoapVarPass = new SoapVar($this->password, XSD_STRING, NULL, $strWSSENS, NULL, $strWSSENS);
        $objSoapVarNonce = new SoapVar($nonce, XSD_STRING, NULL, $strWSSENS, NULL, $strWSSENS);
        $objSoapVarCreated = new SoapVar($created, XSD_STRING, NULL, $strWSSENS, NULL, $strWSSENS);

        $objWSSEAuth = new WsseAuth($objSoapVarUser, $objSoapVarPass, $objSoapVarNonce, $objSoapVarCreated);

        $objSoapVarWSSEAuth = new SoapVar($objWSSEAuth, SOAP_ENC_OBJECT, NULL, $strWSSENS, 'UsernameToken', $strWSSENS);

        $objWSSEToken = new WsseToken($objSoapVarWSSEAuth);

        $objSoapVarWSSEToken = new SoapVar($objWSSEToken, SOAP_ENC_OBJECT, NULL, $strWSSENS, 'UsernameToken', $strWSSENS);

        $objSoapVarHeaderVal = new SoapVar($objSoapVarWSSEToken, SOAP_ENC_OBJECT, NULL, $strWSSENS, 'Security', $strWSSENS);

        $objSoapVarWSSEHeader[] = new SoapHeader($strWSSENS, 'Security', $objSoapVarHeaderVal,true);

        $soapVarServiceVersion = new SoapVar($this->serviceVersion, XSD_STRING, null, null, 'serviceVersion');
        $objSoapVarWSSEHeader[] = new SoapHeader($strService, 'serviceVersion', $soapVarServiceVersion);

        $soapVarLogin = new SoapVar($this->contextLogin, XSD_STRING, null, $strContext, 'login', $strContext);
        $soapVarPassword = new SoapVar($this->contextPassword, XSD_STRING, null, $strContext, 'password', $strContext);
        $soapVarUser = new SoapVar(array($soapVarLogin, $soapVarPassword), SOAP_ENC_OBJECT, null, $strContext, 'user', $strContext);

        $soapVarContext = new SoapVar(array('user' => $soapVarUser), SOAP_ENC_OBJECT, null, $strContext, 'context', $strContext);

        $objSoapVarWSSEHeader[] = new SoapHeader($strContext, 'context', $soapVarContext);

        $this->__setSoapHeaders($objSoapVarWSSEHeader);
    }

    /**
     * @return \SoapVar
     */
    public function setDepositPagination()
    {
        $soapVarResults = new SoapVar(100, XSD_STRING, null, self::$strContext, 'resultsByPage', self::$strContext);
        $soapVarPageNum = new SoapVar(1, XSD_STRING, null, self::$strContext, 'pageNum', self::$strContext);
        $soapVarSortOrder = new SoapVar('DESC', XSD_STRING, null, self::$strContext, 'sortOrder', self::$strContext);
        $soapVarOrderBy = new SoapVar('INTERCHANGE_ID', XSD_STRING, null, self::$strContext, 'orderBy', self::$strContext);
        return new SoapVar(array($soapVarResults, $soapVarPageNum, $soapVarSortOrder, $soapVarOrderBy), SOAP_ENC_OBJECT, null, self::$strContext, 'GetInterchangesByDepositIDPagination', self::$strContext);
    }

    /**
     * @param $identifiant
     *
     * @return SoapVar
     */
    public function setDepositId($identifiant)
    {
        $soapVarDeposit = new SoapVar($identifiant, XSD_STRING, null, self::$strContext, 'depositId', self::$strContext);
        return $soapVarDeposit;
    }

    /**
     * Récupère uniquement l'enveloppe soap
     *
     * @param $response
     * @return mixed
     */
    public function parseResponse($response = null)
    {
        if (is_null($response)) {
            $response = $this->__getLastResponse();
        }
        $start = strpos($response, '<soap:');
        $tmp = substr($response, $start);
        $end = explode('------=', $tmp);
        return $end[0];
    }

    /**
     * Parse the lastResponse
     *
     * @return string
     */
    public function getResponse()
    {
        $resp = new \DOMDocument('1.0', 'utf-8');
        $resp->loadXML($this->parseResponse($this->__getLastResponse()));
        $XMLresults = $resp->getElementsByTagName("wsResponse");

        if ($XMLresults) {
            return $XMLresults->item(0)->nodeValue;
        }
        return false;
    }

    /**
     * Renvoie uniquement la partie enveloppe de la réponse
     *
     * @return string
     */
    public function getLastResponse()
    {
        return $this->parseResponse($this->__getLastResponse());
    }


    /**
     * Custom request method to be able to modify the SOAP messages.
     * $oneWay parameter is not used at the moment.
     *
     * @param string $request Request string
     * @param string $location Location
     * @param string $action SOAP action
     * @param int $version SOAP version
     * @param int $oneWay 0|1
     *
     * @return string
     */
    public function __doRequest($request, $location, $action, $version, $oneWay = 0)
    {
        // wrap request data in SoapRequest object
        $soapRequest = SoapRequest::create($request, $location, $action, $version);

        // do actual SOAP request
        $soapResponse = $this->__doRequest2($soapRequest);

        if (!isset($this->options['attachment_type']) || Helper::ATTACHMENTS_TYPE_MTOM != $this->options['attachment_type']) {
            $resp = $this->parseResponse($soapResponse->getContent());
            $soapResponse->setContent($resp);
        }
        // return SOAP response to ext/soap
        return $soapResponse->getContent();
    }

    /**
     * Runs the currently registered request filters on the request, performs
     * the HTTP request and runs the response filters.
     *
     * @param SoapRequest $soapRequest SOAP request object
     *
     * @return SoapResponse
     */
    protected function __doRequest2(SoapRequest $soapRequest)
    {
        // run SoapKernel on SoapRequest
        $this->soapKernel->filterRequest($soapRequest);

        // perform HTTP request with cURL
        $soapResponse = $this->__doHttpRequest($soapRequest);

        // run SoapKernel on SoapResponse
        $this->soapKernel->filterResponse($soapResponse);

        return $soapResponse;
    }

    /**
     * Filters HTTP headers which will be sent
     *
     * @param SoapRequest $soapRequest SOAP request object
     * @param array $headers An array of HTTP headers
     *
     * @return array
     */
    protected function filterRequestHeaders(SoapRequest $soapRequest, array $headers)
    {
        return $headers;
    }

    /**
     * Adds additional cURL options for the request
     *
     * @param SoapRequest $soapRequest SOAP request object
     *
     * @return array
     */
    protected function filterRequestOptions(SoapRequest $soapRequest)
    {
        return array();
    }

    /**
     * @param SoapRequest $soapRequest
     *
     * @return SoapResponse
     * @throws \SoapFault
     */
    private function __doHttpRequest(SoapRequest $soapRequest)
    {
        // HTTP headers
        $soapVersion = $soapRequest->getVersion();
        $soapAction = $soapRequest->getAction();
        if (SOAP_1_1 == $soapVersion) {
            $headers = array(
                'Content-Type:' . $soapRequest->getContentType(),
                'SOAPAction: "' . $soapAction . '"',
            );
        } else {
            $headers = array(
                'Content-Type:' . $soapRequest->getContentType() . '; action="' . $soapAction . '"',
            );
        }
        $location = $soapRequest->getLocation();
        $content = $soapRequest->getContent();

        $headers = $this->filterRequestHeaders($soapRequest, $headers);

        $options = $this->filterRequestOptions($soapRequest);

        // execute HTTP request with cURL
        $responseSuccessfull = $this->curl->exec(
            $location,
            $content,
            $headers,
            $options
        );

        // tracing enabled: store last request header and body
        if ($this->tracingEnabled === true) {
            $this->lastRequestHeaders = $this->curl->getRequestHeaders();
            $this->lastRequest = $soapRequest->getContent();
        }
        // in case of an error while making the http request throw a soapFault
        if ($responseSuccessfull === false) {
            // get error message from curl
            $faultstring = $this->curl->getErrorMessage();
            throw new \SoapFault('HTTP', $faultstring);
        }
        // tracing enabled: store last response header and body
        if ($this->tracingEnabled === true) {
            $this->lastResponseHeaders = $this->curl->getResponseHeaders();
            $this->lastResponse = $this->curl->getResponseBody();
        }
        // wrap response data in SoapResponse object
        $soapResponse = SoapResponse::create(
            $this->curl->getResponseBody(),
            $soapRequest->getLocation(),
            $soapRequest->getAction(),
            $soapRequest->getVersion(),
            $this->curl->getResponseContentType()
        );
        return $soapResponse;
    }

    /**
     * @param       $wsdl
     * @param array $options
     *
     * @return mixed
     * @throws \SoapFault
     */
    protected function loadWsdl($wsdl, array $options)
    {
        // option to resolve wsdl/xsd includes
        $resolveRemoteIncludes = true;
        if (isset($options['resolve_wsdl_remote_includes'])) {
            $resolveRemoteIncludes = $options['resolve_wsdl_remote_includes'];
        }
        // option to enable cache
        $wsdlCache = WSDL_CACHE_DISK;
        if (isset($options['cache_wsdl'])) {
            $wsdlCache = $options['cache_wsdl'];
        }
        $wsdlDownloader = new WsdlDownloader($this->curl, $resolveRemoteIncludes, $wsdlCache);
        try {
            $cacheFileName = $wsdlDownloader->download($wsdl);
        } catch (\RuntimeException $e) {
            throw new \SoapFault('WSDL', "SOAP-ERROR: Parsing WSDL: Couldn't load from '" . $wsdl . "' : failed to load external entity \"" . $wsdl . "\"");
        }

        return $cacheFileName;
    }
}