<?php
/**
 * OAuth facade
 *
 * @package surveygizmo-api-php
 * @version 0.3
 * @author Nathan Sollenberger <nsollenberger@gmail.com>
 */
namespace spacenate\SurveyGizmo;

use spacenate\SurveyGizmoApiWrapper;
use tmhOAuth;

class OAuth extends tmhOAuth
{
	protected $callback;
	protected $oauth_token;
	protected $oauth_token_secret;
	
    public function __construct(SurveyGizmoApiWrapper $master, $oauth_options) {
        $this->master = $master;
		parent::__construct($oauth_options);
    }
	
	public function configure($config = array())
	{
		if (isset($config["oauth_callback"])) $this->callback = $config["oauth_callback"];
		if (isset($config["oauth_token"])) $this->oauth_token = $config["oauth_token"];
		if (isset($config["oauth_token_secret"])) $this->oauth_token_secret = $config["oauth_token_secret"];
		parent::reconfigure(array_merge($this->config,$config));
	}
	// @todo store oauth_token in session
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
	
	// @todo verify @oauth_token matches stored token
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
	
	public function setTokenAndSecret( $oauth_token, $oauth_token_secret )
	{
		$this->reconfigure(array_merge($this->config, array(
			"token" => $oauth_token,
			"secret" => $oauth_token_secret
		)));
		$this->master->setAuthTypeOAuth();
	}
}

