<?php
/**
 * SurveyGizmo REST API wrapper
 *
 * @package surveygizmo-api-php
 * @version 0.3
 * @author Nathan Sollenberger <nsollenberger@gmail.com>
 */
namespace spacenate;

require_once 'SurveyGizmo/OAuth.php';
require_once 'SurveyGizmo/Survey.php';
require_once 'SurveyGizmo/SurveyPage.php';
require_once 'SurveyGizmo/SurveyQuestion.php';
require_once 'SurveyGizmo/SurveyOption.php';

/**
 * SurveyGizmoApiWrapper class accepts credentials and allows
 * access to API objects
 *
 * @package surveygizmo-api-php
 */
class SurveyGizmoApiWrapper
{

    protected $email;
    protected $password;
    protected $auth_type;
    protected $domain;
    protected $version;
    protected $format;
    protected $ch;
    protected $debug;

    /**
     * Constructor sets options, initializes curl handler as well as API objects
     *
     * @param string $email (optional) email address to authenticate with
     * @param string $password (optional) md5 or plaintext password to authenticate with
     * @param string $auth_type (optional) which auth type to use, "md5" or "pass"
     * @param array $opts (optional) key=>value array using one or more of the following keys:
     *        - timeout int connection timeout limit
     *        - debug bool enable debug logging
     *        - version string either head, v3, or v4
     *        - domain string domain name to make api calls against
     */
	// @todo move all options to config array
    public function __construct( $email = "", $password = "", $auth_type = "pass", $opts = array() )
    {
        if ($email && $password) {
            $this->email = $email;
            $this->password = $password;
            $this->auth_type = $auth_type;
        }

        $this->domain = (!isset($opts['domain']) || !is_string($opts['domain'])) ? "restapi.surveygizmo.com" : $opts['domain'];
        $this->version = (!isset($opts['version']) || !is_string($opts['version'])) ? "head" : $opts['version'];

        if (!isset($opts['timeout']) || !is_int($opts['timeout'])) {
            $opts['timeout'] = 60;
        }
        if (isset($opts['debug']) && $opts['debug']) {
            $this->debug = true;
        }
        if (isset($opts['format']) && in_array($opts['format'], array("json", "pson", "xml", "debug"))) {
            $this->format = $opts['format'];
        }
        else {
            $this->format = "json";
        }

        $this->ch = curl_init();

        curl_setopt($this->ch, CURLOPT_USERAGENT, 'SurveyGizmo-API-PHP/0.0.3');
        curl_setopt($this->ch, CURLOPT_HEADER, false);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $opts['timeout']);

        $this->surveys = new SurveyGizmo\Survey($this);
		$this->pages = $this->surveypages = new SurveyGizmo\SurveyPage($this);
        $this->questions = $this->surveyquestions = new SurveyGizmo\SurveyQuestion($this);
		$this->options = $this->surveyoptions = new SurveyGizmo\SurveyOption($this);
		
		$oauth_config = array
		(
	        'user_agent'                 => 'SurveyGizmo-API-PHP/0.0.3',
	        'host'                       => 'restapi.surveygizmo.com/' . $this->version
		);
		$this->oauth = new SurveyGizmo\OAuth($this, $oauth_config);
    }

    /**
     * Destructor closes curl handler
     */
    public function __destruct() {
        if(is_resource($this->ch)) {
            curl_close($this->ch);
        }
    }

    /**
     * Specify the credentials to use when connecting to the API
     *
     * @param string $email email address to authenticate with
     * @param string $password md5 or plaintext password to authenticate with
     * @param string $auth_type (optional) which auth type to use, "md5" or "pass". Defaults to "pass"
     */
    public function setCredentials( $email, $password, $auth_type = "pass" )
    {
        $this->email = $email;
        $this->password = $password;
        $this->auth_type = $auth_type;
    }
	
	public function setAuthTypeOAuth()
	{
		$this->auth_type = "oauth";
	}

    /**
     * Get credential parameter string
     *
     * @return string|bool query string formatted credentials, or false if credentials are not set
     */
    public function getCredentials()
    {
        if (isset($this->email) && isset($this->password)) {
            switch ($this->auth_type) {
				case "oauth":
					return false;
                case "md5":
                    return "user:md5=" . $this->email . ":" . $this->password;
                default:
                    return "user:pass=" . $this->email . ":" . $this->password;
            }
        }
        else return false;
    }

    /**
     * Attempts to authenticate the supplied plaintext/md5 or oauth credentials
     *
     * @return bool whether redentials authenticated
     */
    public function testCredentials()
    {
		$params = array('page'=>1,'resultsperpage'=>0);
		if ($this->auth_type === "oauth") {
			$this->oauth->request("GET", "https://{$this->domain}/{$this->version}/survey", $params);
			$output = $this->oauth->response['response'];
		} else {
	        $params = http_build_query($params);
	        $output = $this->call('survey', 'GET', $params, 'json');
		}
		$output = json_decode($output);
        if (isset($output->result_ok) && $output->result_ok) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Set the return format to JSON, PSON, XML, or debug
     *
     * @param string $format either "json", "pson", "xml", or "debug"
     */
    public function setFormat( $format )
    {
        if (in_array($opts['format'], array("json", "pson", "xml", "debug"))) {
            $this->format = $opts['format'];
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
	 * Sends HTTP request using $method to specified $endPoint
	 *
	 * @param string $endPoint path to append to (base url+version)
	 * @param string $method (optional) HTTP method to use
	 * @param string $params (optional) query string formatted parameters
	 * @param string $format (optional) format to request
	 * @return SG API object
	 */
    public function call($endPoint, $method = "GET", $params = "", $format = "")
    {
        $format = in_array($format, array("json", "pson", "xml", "debug")) ? $format : $this->format;
        $url    = "https://{$this->domain}/{$this->version}/{$endPoint}.{$format}";
		
		if ($this->auth_type === "oauth") {
			$this->log("Using OAuth");
			// turn query string in to key=>value array
			parse_str($params, $params);
			$this->oauth->request($method, $url, $params);
			return $this->oauth->response['response'];
		}
		
        $creds  = $this->getCredentials();
        if (!$creds) return false;

        $ch = $this->ch;
        $url .= "?_method={$method}&{$creds}";
        if ($params) $url .= '&' . $params;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, $this->debug);

        $start = microtime(true);
        $this->log('Call to ' . $url);
        if($this->debug) {
            $curl_buffer = fopen('php://memory', 'w+');
            curl_setopt($ch, CURLOPT_STDERR, $curl_buffer);
        }

        $response_body = curl_exec($ch);

        $info = curl_getinfo($ch);
        $time = microtime(true) - $start;
        if($this->debug) {
            rewind($curl_buffer);
            $this->log(stream_get_contents($curl_buffer));
            fclose($curl_buffer);
        }
        $this->log('Completed in ' . number_format($time * 1000, 2) . 'ms');
        $this->log('Got response: ' . $response_body);

        if(curl_error($ch)) {
            throw new Exception("API call to $url failed: " . curl_error($ch));
        }

        if(floor($info['http_code'] / 100) >= 4) {
            throw new Exception($result);
        }

        return $response_body;
    }

    public function log( $msg )
    {
        if ($this->debug)
            print $msg . "\n";
    }

}

