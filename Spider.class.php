<?php

require_once('util4p/CRObject.class.php');

class Spider
{
	private $userAgent = 'Spider';
	private $accept = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8';
	private $acceptEncoding = 'gzip, deflate, br';
	private $acceptLanguage = 'en-US,en;q=0.9,zh-CN;q=0.8,zh;q=0.7';
	private $cookie = '';
	private $referer = '';

	private $timeout = 15;
	private $headers = array();
	private $body = '';

	private $info = array();


	public function configure(CRObject $config)
	{
		$this->userAgent = $config->get('User-Agent', $this->userAgent);
		$this->accept = $config->get('Accept', $this->accept);
		$this->acceptEncoding = $config->get('Accept-Encoding', $this->acceptEncoding);
		$this->acceptLanguage = $config->get('Accept-Encoding', $this->acceptLanguage);
		$this->cookie = $config->get('Cookie', $this->cookie);
		$this->referer = $config->get('Referer', $this->referer);
		$this->timeout = $config->get('timeout', $this->timeout);
	}

	public function doGet($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout - 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); //避免data数据过长问题
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		$ret = curl_exec($ch);
		$err = curl_error($ch);
		if (!$err) {
			$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$header = substr($ret, 0, $header_size);
			$headers = array();
			// Split the string on every "double" new line.
			$arrRequests = explode("\r\n\r\n", $header);
			// Loop of response headers. The "count() -1" is to avoid an empty row for the extra line break before the body of the response.
			for ($index = 0; $index < count($arrRequests) - 1; $index++) {
				foreach (explode("\r\n", $arrRequests[$index]) as $i => $line) {
					if ($i === 0)
						$headers[$index]['http_code'] = $line;
					else {
						list ($key, $value) = explode(': ', $line);
						$headers[$index][$key] = $value;
					}
				}
			}
			$this->headers = $headers[max(0, count($headers) - 1)];
			$this->body = substr($ret, $header_size);
			$this->info = curl_getinfo($ch);
		}
		return !$err;
	}

	/*
	 *
	 * @param $url string
	 * @param $post_data array('key' => 'value')
	 *
	 * */
	public function doPost($url, $post_data)
	{
		$fields_string = http_build_query($post_data);
		//open connection
		$ch = curl_init();
		//set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout - 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch, CURLOPT_HEADER, 1);

		$ret = curl_exec($ch);
		$err = curl_error($ch);
		if (!$err) {
			$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$header = substr($ret, 0, $header_size);
			$headers = array();
			// Split the string on every "double" new line.
			$arrRequests = explode("\r\n\r\n", $header);
			// Loop of response headers. The "count() -1" is to avoid an empty row for the extra line break before the body of the response.
			for ($index = 0; $index < count($arrRequests) - 1; $index++) {
				foreach (explode("\r\n", $arrRequests[$index]) as $i => $line) {
					if ($i === 0)
						$headers[$index]['http_code'] = $line;
					else {
						list ($key, $value) = explode(': ', $line);
						$headers[$index][$key] = $value;
					}
				}
			}
			$this->headers = $headers[max(0, count($headers) - 1)];
			$this->body = substr($ret, $header_size);
			$this->info = curl_getinfo($ch);
		}
		return !$err;
	}

	public function getHeader($key)
	{
		return $key;
	}

	public function getBody()
	{
		return $this->body;
	}

	public function getStatusCode()
	{
		return $this->info['http_code'];
	}
}