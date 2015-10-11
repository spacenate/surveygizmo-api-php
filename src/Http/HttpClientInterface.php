<?php
/**
 * HTTP client interface
 *
 * @package surveygizmo-api-php
 * @author Nathan Sollenberger <nate@spacenate.com>
 */
namespace spacenate\Http;

interface HttpClientInterface
{
	public function sendRequest( $uri );
	public function getStatusCode();
	public function getResponseBody();
}