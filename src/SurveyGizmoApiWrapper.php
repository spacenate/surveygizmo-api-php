<?php
/**
 * SurveyGizmo REST API wrapper
 *
 * @package surveygizmo-api-php
 * @author Nathan Sollenberger <nate@spacenate.com>
 */
namespace spacenate;

use spacenate\Http\HttpClientInterface;
use spacenate\Http\RequestsClient;

require_once 'Http/HttpClientInterface.php';
require_once 'Http/RequestsClient.php';
require_once 'SurveyGizmo/Account.php';
require_once 'SurveyGizmo/AccountTeams.php';
require_once 'SurveyGizmo/AccountUser.php';
require_once 'SurveyGizmo/EmailMessage.php';
require_once 'SurveyGizmo/OAuth.php';
require_once 'SurveyGizmo/Survey.php';
require_once 'SurveyGizmo/SurveyCampaign.php';
require_once 'SurveyGizmo/SurveyOption.php';
require_once 'SurveyGizmo/SurveyPage.php';
require_once 'SurveyGizmo/SurveyQuestion.php';
require_once 'SurveyGizmo/SurveyResponse.php';

/**
 * SurveyGizmoApiWrapper class accepts credentials and allows
 * access to API objects
 *
 * @package surveygizmo-api-php
 */
class SurveyGizmoApiWrapper
{

    public $Account;
    public $AccountTeams;
    public $AccountUser;
    //public $ContactList;
    public $Survey;
    public $SurveyPage;
    public $SurveyQuestion;
    public $SurveyOption;
    public $SurveyCampaign;
    //public $Contact;
    public $EmailMessage;
    public $SurveyResponse;
    //public $SuveyStatistic;
    //public $SurveyReport;

    protected $userId;
    protected $secret;
    protected $authType;
    protected $domain;
    protected $version;
    protected $format;
    protected $httpClient;
    protected $debug;

    /**
     * Constructor sets options, initialize API objects
     *
     * @param string $userId (optional) API token, email address, or OAuth access token to authenticate with
     * @param string $secret (optional) API token secret, md5, plaintext password or OAuth access token secret to authenticate with
     * @param string $authType (optional) which authentication type to use, "api_token" or "oauth". Defaults to "api_token"
     * @param array $opts (optional) key-value pairs of one or more of the following keys:
     *        - "timeout" int connection timeout limit
     *        - "debug" bool enable debug logging
     *        - "version" string either head, v3, or v4
     *        - "domain" string domain name to make api calls against
     */
    // @todo move all options to config array, with logic in its own method!
    public function __construct( $userId = null, $secret = null, $authType = null, $opts = array() )
    {
        if ($userId) {
            $this->setCredentials($userId, $secret, $authType);
        }

        $this->domain = (!isset($opts['domain']) || !is_string($opts['domain'])) ? "restapi.surveygizmo.com" : $opts['domain'];
        $this->version = (!isset($opts['version']) || !is_string($opts['version'])) ? "v5" : $opts['version'];

        if (!isset($opts['timeout']) || !is_int($opts['timeout'])) {
            $opts['timeout'] = 60;
        }
        if (isset($opts['debug']) && $opts['debug']) {
            $this->debug = true;
        }
        if (isset($opts['format']) && in_array($opts['format'], array("json", "pson", "xml", "debug"))) {
            $this->format = $opts['format'];
        } else {
            $this->format = "json";
        }
        if (isset($opts['httpClient']) && $opts['httpClient'] instanceof HttpClientInterface) {
            $this->httpClient = $opts['httpClient'];
        } else {
            $this->httpClient = new RequestsClient();
        }

        $this->Account = new SurveyGizmo\Account($this);
        $this->AccountTeams = new SurveyGizmo\AccountTeams($this);
        $this->AccountUser = new SurveyGizmo\AccountUser($this);
        //$this->ContactList = new SurveyGizmo\ContactList($this);
        $this->Survey = new SurveyGizmo\Survey($this);
        $this->SurveyPage = new SurveyGizmo\SurveyPage($this);
        $this->SurveyQuestion = new SurveyGizmo\SurveyQuestion($this);
        $this->SurveyOption = new SurveyGizmo\SurveyOption($this);
        $this->SurveyCampaign = new SurveyGizmo\SurveyCampaign($this);
        //$this->Contact = new SurveyGizmo\Contact($this);
        $this->EmailMessage = new SurveyGizmo\EmailMessage($this);
        $this->SurveyResponse = new SurveyGizmo\SurveyResponse($this);
        //$this->SuveyStatistic = new SurveyGizmo\SuveyStatistic($this);
        //$this->SurveyReport = new SurveyGizmo\SurveyReport($this);

        // tmhOAuth config
        $oauth_config = array(
            'user_agent'    => 'SurveyGizmo-API-PHP/0.3',
            'host'          => $this->domain . $this->version
        );
        $this->oauth = new SurveyGizmo\OAuth($this, $oauth_config);
    }

    /**
     * Specify the credentials to use when connecting to the API
     *
     * @param string $userId API token, email address, or OAuth access token to authenticate with
     * @param string $secret (optional) API token secret or OAuth access token secret to authenticate with
     * @param string $authType (optional) which authentication type to use, "api_token" or "oauth". Defaults to "api_token"
     * @return bool credentials set successfully
     */
    public function setCredentials( $userId, $secret = null, $authType = null )
    {
        if ($authType === null) {
            $authType = "api_token";
        }
        if (!in_array($authType, array("api_token", "oauth"))) {
            return false;
        }

        if ("oauth" === $authType) {
            return $this->oauth->setTokenAndSecret($userId, $secret);
            // setTokenAndSecret also calls SurveyGizmoApiWrapper::setAuthTypeOAuth()
        } else {
            $this->userId = $userId;
            $this->secret = $secret;
            $this->authType = $authType;
            return true;
        }
    }

    /**
     * Authenticate using OAuth
     *
     * A validated Access Token and Token Secret must be set first
     * using spacenate\SurveyGizmo\OAuth::setTokenAndSecret()
     *
     * @return null
     */
    public function setAuthTypeOAuth()
    {
        $this->authType = "oauth";
    }

    /**
     * Get credential parameter string
     *
     * @return string|bool query string formatted credentials, or false if credentials are not set
     */
    public function getCredentials()
    {
        if (isset($this->userId)) {
            switch ($this->authType) {
                case "oauth":
                    return false;
                case "api_token":
                default:
                    $token_secret = ($this->secret) ? "&api_token_secret=" . $this->secret : "";
                    return "api_token=" . $this->userId . $token_secret;
            }
        } else {
            return false;
        }
    }

    /**
     * Attempts to authenticate the supplied plaintext/md5 or oauth credentials
     *
     * @return bool credentials authenticated successfully
     */
    public function testCredentials()
    {
        $params = array('page'=>1,'resultsperpage'=>0);
        if ($this->authType === "oauth") {
            $this->oauth->request("GET", "https://{$this->domain}/{$this->version}/survey", $params);
            $output = $this->oauth->response['response'];
        } else {
            $params = http_build_query($params);
            $output = $this->call('survey', 'GET', $params, 'json');
        }
        $output = json_decode($output);
        if (isset($output->result_ok) && $output->result_ok) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Set the return format to JSON, PSON, XML, or debug
     *
     * @param string $format either "json", "pson", "xml", or "debug"
     * @return bool format set successfully
     */
    public function setOutputFormat( $format )
    {
        if (in_array($format, array("json", "pson", "xml", "debug"))) {
            $this->format = $format;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Convert multidimensional array to filter string
     *
     * @param array $filters multidimensional array [[field, operator, value], ...]
     * @return string query string formatted filter
     */
    public function getFilterString( $filters )
    {
        if (!is_array($filters) || count($filters) == 0) {
            return "";
        }
        if (!is_array($filters[0])) {
            $filters = array($filters);
        }
        $return = array();
        $i = 0;
        foreach ($filters as $filter) {
            if (count($filter) !== 3) {
                continue;
            }
            $return[] = "filter[field][{$i}]={$filter[0]}"
                    . "&filter[operator][{$i}]={$filter[1]}"
                    . "&filter[value][{$i}]={$filter[2]}";
            $i++;
        }
        return implode("&", $return);
    }

    /**
     * Filter an array of paramaters
     *
     * @param array $params array of paramaters to filter
     * @param array $allowed array of allowed parameter keys
     * @param array $allowed_regxp (optional) array of allowed parameter key Regular Expressions
     * @return array Filtered array of valid paramaters
     */
    public function getValidParameters( $params, $allowed, $allowed_regxp = array() )
    {
        if (!is_array($params) || count($params) === 0)
            return array();

        foreach ($params as $key => $value) {
            if(!is_string($key) || !in_array($key, $allowed)) {
                // key was not found in $allowed, check regex patterns
                foreach ($allowed_regxp as $pattern) {
                    if (preg_match($pattern, $key)) {
                        // matched a pattern! break out of the $allowed_regxp foreach
                        // and continue with the next key in the $params foreach
                        continue 2;
                    }
                }
                unset($params[$key]);
            }
        }
        return $params;
    }

    /**
     * Sends HTTP request using $method to specified $endPoint
     *
     * @param string $endPoint path to append to (base url+version)
     * @param string $method (optional) HTTP method to use
     * @param string $params (optional) query string formatted parameters
     * @param string $format (optional) format to request
     * @return string SG API object
     * @throws ErrorException
     */
    public function call( $endPoint, $method = "GET", $params = "", $format = "" )
    {
        $format = in_array($format, array("json", "pson", "xml", "debug")) ? $format : $this->format;
        $url    = "https://{$this->domain}/{$this->version}/{$endPoint}.{$format}";

        if ($this->authType === "oauth") {
            $this->log("Using OAuth");
            $this->log("Request: $method $url");
            $this->log("Params: $params");

            // turn query string in to key=>value array
            parse_str($params, $params);
            $this->oauth->request($method, $url, $params);
            
            return $this->oauth->response['response'];
        }

        $creds  = $this->getCredentials();
        // throw error
        if (!$creds) {
            throw new ErrorException('Missing API credentials');
        }

        $this->log("Request: $method $url");
        $this->log("Params: $params");

        $url .= "?_method={$method}&{$creds}";
        if ($params) $url .= '&' . $params;

        $this->httpClient->sendRequest($url);
        if ($this->httpClient->getStatusCode() !== 200) {
            // handle errors
            return false;
        }
        return $this->httpClient->getResponseBody();
    }

    /**
     * Prints log messages if $this->debug is true
     *
     * Will most likely end up delegating the actual task of logging to some externally provided logger
     *
     * @param string $msg message to log
     */
    public function log( $msg )
    {
        if ($this->debug)
            print $msg . "\n";
    }
}
