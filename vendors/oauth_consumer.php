<?php
/**
 * A simple OAuth consumer for CakePHP.
 * 
 * Requires the OAuth library from http://oauth.googlecode.com/svn/code/php/
 * 
 * Copyright (c) by Daniel Hofstetter (http://cakebaker.42dh.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 */

App::import('Vendor', 'oauth', array('file' => 'OAuth'.DS.'OAuth.php'));
App::import('Core', 'http_socket');

// using an underscore in the class name to avoid a naming conflict with the OAuth library
class OAuth_Consumer { 
	private $url = null;
	private $consumerKey = null;
	private $consumerSecret = null;
	
	public function __construct($consumerKey, $consumerSecret = '') {
		$this->consumerKey = $consumerKey;
		$this->consumerSecret = $consumerSecret;
	}

	/**
	 * Call API with a GET request
	 */
	public function get($accessTokenKey, $accessTokenSecret, $url, $getData = array()) {
		$accessToken = new OAuthToken($accessTokenKey, $accessTokenSecret);
		$request = $this->createRequest('GET', $url, $accessToken, $getData);
		
		return $this->doGet($request->to_url());
	}
	
	public function getAccessToken($accessTokenURL, $requestToken, $httpMethod = 'POST', $parameters = array()) {
		$this->url = $accessTokenURL;
		$request = $this->createRequest($httpMethod, $accessTokenURL, $requestToken, $parameters);
		
		return $this->doRequest($request);
	}
	
	public function getRequestToken($requestTokenURL, $httpMethod = 'POST', $parameters = array()) {
		$this->url = $requestTokenURL;
		$request = $this->createRequest($httpMethod, $requestTokenURL, null, $parameters);
		
		return $this->doRequest($request);
	}
	
	/**
	 * Call API with a POST request
	 */
	public function post($accessTokenKey, $accessTokenSecret, $url, $postData = array()) {
		$accessToken = new OAuthToken($accessTokenKey, $accessTokenSecret);
		$request = $this->createRequest('POST', $url, $accessToken, $postData);
		
		return $this->doPost($url, $request->to_postdata());
	}
	
	protected function createOAuthToken($response) {
		if (isset($response['oauth_token']) && isset($response['oauth_token_secret'])) {
			return new OAuthToken($response['oauth_token'], $response['oauth_token_secret']);
		}
		
		return null;
	}
	
	private function createConsumer() {
		return new OAuthConsumer($this->consumerKey, $this->consumerSecret);
	}
	
	private function createRequest($httpMethod, $url, $token, array $parameters) {
		$consumer = $this->createConsumer();
		$request = OAuthRequest::from_consumer_and_token($consumer, $token, $httpMethod, $url, $parameters);
		$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $token);
		
		return $request;
	}

	private function doGet($url) {
		$socket = new HttpSocket();
		return $socket->get($url);
	}
	
	private function doPost($url, $data) {
		$socket = new HttpSocket();
		return $socket->post($url, $data);
	}
	
	private function doRequest($request) {
		if ($request->get_normalized_http_method() == 'POST') {
			$data = $this->doPost($this->url, $request->to_postdata());
		} else {
			$data = $this->doGet($request->to_url());
		}

		$response = array();
		parse_str($data, $response);

		return $this->createOAuthToken($response);
	}
}
