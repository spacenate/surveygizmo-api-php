<?php
/**
 * SurveyGizmo REST API wrapper
 *
 * @package surveygizmo-api-php
 * @version 0.1
 * @author Nathan Sollenberger <nsollenberger@gmail.com>
 */
namespace spacenate;

require_once 'SurveyGizmo/Survey.php';
require_once 'SurveyGizmo/SurveyQuestion.php';

/**
 * SurveyGizmoApiWrapper class accepts credentials and allows
 * access to API objects
 *
 * @package surveygizmo-api-php
 */
class SurveyGizmoApiWrapper
{

    private $email;
    private $password;
    private $auth_type;
    private $domain;
    private $version;
    private $return_assoc;
    private $ch;
    private $debug;

    /**
     * Constructor sets options, initializes curl handler as well as API objects
     *
     * @param email string optional email address to authenticate with
     * @param password string optional md5 or plaintext password
     * @param auth_type string optional "md5" or "plaintext"
     * @param opts array optional containing one of the following keys
     *     - timeout int connection timeout limit
     *     - debug bool enable debug logging
     *     - version string either head, v3, or v4
     *     - domain string domain name to make api calls against
     */
    public function __construct( $email = "", $password = "", $auth_type = "md5", $opts = array() )
    {
        if ($email && $password) {
            $this->email = $email;
            $this->password = $password;
            $this->auth_type = $auth_type;
        }

        $this->domain = (!isset($opts['domain']) || !is_string($opts['domain'])) ? "restapi.surveygizmo.com" : $opts['domain'];
        $this->version = (!isset($opts['version']) || !is_string($opts['version'])) ? "head" : $opts['version'];

        if (!isset($opts['timeout']) || !is_int($opts['timeout'])) {
            $opts['timeout'] = 600;
        }
        if (isset($opts['debug']) && $opts['debug']) {
            $this->debug = true;
        }
        if (isset($opts['assoc']) && $opts['assoc']) {
            $this->return_assoc = true;
        }

        $this->ch = curl_init();

        curl_setopt($this->ch, CURLOPT_USERAGENT, 'SurveyGizmo-API-PHP/0.0.1');
        //curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_HEADER, false); // ??
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $opts['timeout']);

        $this->surveys = new SurveyGizmo\Survey($this);
        $this->questions = $this->surveyquestions = new SurveyGizmo\SurveyQuestion($this);
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
     * @param email string email address to authenticate with
     * @param password string md5 or plaintext password
     * @param auth_type string "md5" or "plaintext"
     */
    public function setCredentials( $email, $password, $auth_type = "md5" )
    {
        $this->email = $email;
        $this->password = $password;
        $this->auth_type = $auth_type;
    }

    /**
     * Get credential parameter string
     *
     * @return string|bool query string formatted credentials, or false if not set
     */
    public function getCredentials()
    {
        if (isset($this->email) && isset($this->password)) {
            switch ($this->auth_type) {
                case "md5":
                    return "user:md5=" . $this->email . ":" . $this->password;
                default:
                    return "user:pass=" . $this->email . ":" . $this->password;
            }
        }
        else return false;
    }

    /**
     * Attempts to authenticate the supplied credentials
     *
     * @return bool Credentials authenticated
     */
    public function testCredentials()
    {
        $output = $this->call('accountuser', 'GET', array('page'=>1,'resultsperpage'=>0));

        if ( (isset($output->result_ok) && $output->result_ok) ||
             (isset($output['result_ok']) && $output['result_ok']) ) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Convert multidimensional array to filter string
     *
     * @param filters array [[field, operator, value], …]
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

    public function call($endPoint, $method = "GET", $params = "") {

        $creds  = $this->getCredentials();
        if (!$creds) return false;
        $ch     = $this->ch;
        $url    = "https://{$this->domain}/{$this->version}/" . $endPoint
                . '.json?_method=' . $method
                . '&' . $creds;
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
        $assoc = ($this->return_assoc) ? 1 : 0;
        $result = json_decode($response_body, $assoc);

        if(floor($info['http_code'] / 100) >= 4) {
            throw $this->castError($result);
        }

        return $result;
    }

    public function log( $msg )
    {
        if ($this->debug)
            print $msg;
    }

}

