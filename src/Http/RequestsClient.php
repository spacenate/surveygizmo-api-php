<?php
/**
 * Implementation of HttpClientInterface using rmccue/requests
 *
 * @package surveygizmo-api-php
 * @author Nathan Sollenberger <tacos@spacenate.com>
 */
namespace spacenate\Http;

use Requests;

class RequestsClient implements HttpClientInterface
{
	protected $response;
	
	public function sendRequest( $uri )
	{
		$this->response = Requests::get($uri);
	}
	
	public function getStatusCode()
	{
		return $this->response->status_code;
	}
	
	public function getResponseBody()
	{
		return (string) $this->response->body;
	}
}