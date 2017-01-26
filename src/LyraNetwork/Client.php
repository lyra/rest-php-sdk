<?php
namespace LyraNetwork;

use LyraNetwork\Exceptions\LyraNetworkException;
use LyraNetwork\Constants;

class Client
{
    private $_login = null;
    private $_password = null;
    private $_privateKey = null;
    private $_publicKey = null;
    private $_endpoint = Constants::END_POINT;
    private $_connectionTimeout = 45;
    private $_timeout = 45;
    private $_proxyHost = null;
    private $_proxyPort = null;

    function __construct($private_key) {
        $auth = explode(':', $private_key);

        if (count($auth) != 2) {
            throw new LyraNetworkException("invalid private key: " . $private_key);
        }

        $this->_privateKey = $private_key;
        $this->_login = $auth[0];
        $this->_password = $auth[1];
    }

    public function getVersion() {
        return Constants::SDK_VERSION;
    }

    public function setPublicKey($publicKey) {
        $this->_publicKey = $publicKey;
    }

    public function getPublicKey() {
        return $this->_publicKey;
    }

    public function setProxy($host, $port) {
        $this->_proxyHost = $host;
        $this->_proxyPort = $port;
    }

    public function setTimeOuts($connectionTimeout, $timeout) {
        $this->_connectionTimeout = $connectionTimeout;
        $this->_timeout = $timeout;
    }

    public function post($target, $array)
    {
        if (extension_loaded('curl')) {
            return $this->postWithCurl($target, $array);
        } else {
            return $this->postWithFileGetContents($target, $array);
        }
    }

    public function postWithCurl($target, $array)
    {
        $url = $this->_endpoint . $target;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Krypton PHP SDK ' + Constants::SDK_VERSION);
        curl_setopt($curl, CURLOPT_USERPWD, $this->_privateKey);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($array));
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT , $this->_connectionTimeout);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->_timeout);

        if($this->_proxyHost && $this->_proxyPort) {
          curl_setopt($curl, CURLOPT_PROXY, $this->_proxyHost);
          curl_setopt($curl, CURLOPT_PROXYPORT, $this->_proxyPort);
        }

        $raw_response = curl_exec($curl);

        /**
         * CA ROOT misconfigured, we try with a local CA bundle
         * It's a common error with a WAMP local server
         */
        if (curl_errno($curl) == 60 && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            curl_setopt($curl, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
            $raw_response = curl_exec($curl);
        }
                  

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $allowedCode = array(200, 401);
        $response = json_decode($raw_response , true);

        if ( !in_array($status, $allowedCode) || is_null($response)) {
            throw new LyraNetworkException("Error: call to URL $url failed with status $status, response $raw_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
        }

        curl_close($curl);

        return $response;
    }

    public function postWithFileGetContents($target, $array)
    {
        $url = $this->_endpoint . $target;

        $http = array(
            'method'        => 'POST',
            'header'        => 'Authorization: Basic ' . base64_encode($this->_privateKey) . "\r\n".
                              'Content-Type: application/json',
            'content'       => json_encode($array),
            'user_agent'    => 'Krypton PHP SDK fallback ' + Constants::SDK_VERSION,
            'timeout'       => $this->_timeout,
            'ignore_errors' => true
        );

        if($this->_proxyHost && $this->_proxyPort) {
            $http['proxy'] = $this->_proxyHost . ':' . $this->_proxyPort;
        }

        $context = stream_context_create(array('http' => $http));
        $raw_response = file_get_contents($url, false, $context);

        if (!$raw_response) {
            throw new Exception("Error: call to URL $url failed.");
        }

        $response = json_decode($raw_response, true);

        if (!response) {
            throw new Exception("Error: call to URL $url failed.");
        }

        return $response;
    }
}