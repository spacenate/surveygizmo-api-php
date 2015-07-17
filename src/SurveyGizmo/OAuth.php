<?php
/**
 * OAuth facade
 *
 * @package surveygizmo-api-php
 * @version 0.3.5
 * @author Nathan Sollenberger <nsollenberger@gmail.com>
 */
namespace spacenate\SurveyGizmo;

use spacenate\SurveyGizmoApiWrapper;
use tmhOAuth;

/**
 * OAuth class extends themattharris/tmhOAuth, providing some convenience methods for
 * interacting with the SurveyGizmo OAuth service
 *
 * @package surveygizmo-api-php
 */
class OAuth extends tmhOAuth
{
    protected $callback;
    protected $oauth_token;
    protected $oauth_token_secret;

    public function __construct(SurveyGizmoApiWrapper $master, $oauth_options) {
        $this->master = $master;
        parent::__construct($oauth_options);
    }

    /**
     * Adds configs to tmhOAuth->config
     *
     * @param array $config key-value pairs of configuration parameters to add
     */
    public function configure($config = array())
    {
        if (isset($config["oauth_callback"])) $this->callback = $config["oauth_callback"];
        if (isset($config["oauth_token"])) $this->oauth_token = $config["oauth_token"];
        if (isset($config["oauth_token_secret"])) $this->oauth_token_secret = $config["oauth_token_secret"];
        parent::reconfigure(array_merge($this->config,$config));
    }

    /**
     * Request an OAuth Request Token
     *
     * This is the first step to authenticating via OAuth. Step two is to 
	 * send the User to SurveyGizmo with this request token. After confirming access,
	 * the User is sent to your callback URL with the same token plus a verifier
	 *
	 * @return array containing the following keys:
	 *         - "oauth_callback_confirmed" (not used?)
	 *         - "oauth_token"
	 *         - "oauth_token_secret" (not used?)
	 *         - "xoauth_token_ttl"
     */
	// @todo ensure returned errors are consistent across wrapper
    public function getRequestToken()
    {
        $code = $this->user_request(array(
            'url' => $this->url('oauth/request_token', ''),
            'params' => array(
                'oauth_callback' => $this->callback
            )
        ));
        if ($code != 200) {
            return "There was an error communicating with SurveyGizmo. {$this->response['response']}";
        }

        return $this->extract_params($this->response['response']);
    }

    /**
     * Exchange a verified Request Token for an Access Token
     *
     * This is step three to authenticating via OAuth. After making sure
	 * this token matches the one originally generated, it and the verifier
	 * are exchanged for an Access Token and Access Token Secret.
	 *
	 * @param string $oauth_token included when SurveyGizmo sends User to registered callback URL 
	 * @param string $oauth_verifier included when SurveyGizmo sends User to registered callback URL
	 * @return array containing the following keys:
	 *         - "oauth_token" Access Token
	 *         - "oauth_token_secret" Access Token Secret
     */
    public function getAccessToken($oauth_token, $oauth_verifier)
    {
        $code = $this->user_request(array(
            'url' => $this->url('oauth/access_token', ''),
            'params' => array(
                  'oauth_token' => $oauth_token,
                  'oauth_verifier' => $oauth_verifier
                )
        ));
        if ($code != 200) {
            return "There was an error communicating with SurveyGizmo. {$this->response['response']}";
        }

        return $this->extract_params($this->response['response']);
    }

    /**
     * Authenticate API calls with the supplied Access Token and Access Token Secret
	 *
	 * @param string $oauth_token Access Token received after completing step three of OAuth process
	 * @param string $oauth_token_secret Access Token Secret received after completing step three of OAuth process
	 * @param bool token and secret successfully set
     */
    public function setTokenAndSecret( $oauth_token, $oauth_token_secret )
    {
        $this->reconfigure(array_merge($this->config, array(
            "token" => $oauth_token,
            "secret" => $oauth_token_secret
        )));
        $this->master->setAuthTypeOAuth();
		return true;
    }
}

