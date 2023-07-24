<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Http
{
    /**
     * GET constant value.
     */
    const TYPE_GET = 'GET';

    /**
     * POST constant value.
     */
    const TYPE_POST = 'POST';

    /**
     * PUT constant value.
     */
    const TYPE_PUT = 'PUT';

    /**
     * PATCH constant value.
     */
    const TYPE_PATCH = 'PATCH';

    /**
     * DELETE constant value.
     */
    const TYPE_DELETE = 'DELETE';

    /**
     * Hold response raw body.
     *
     * @var mixed
     */
    private $body;

    /**
     * Hold response code
     *
     * @var int
     */
    private $statusCode;

    /**
     * Hold response raw headers.
     *
     * @var string
     */
    private $headers;

    /**
     * Default cURL options
     *
     * @var array
     */
     private $options;

    /**
     * Constructor
     *
     * @var array
     */
    public function __construct($curlOptions = []){
        $default = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false
        ];

        $this->options = array_replace($default, $curlOptions);

        // Must required for get headers and body
        $this->options[CURLOPT_HEADER] = 1;
	}

    /**
     * Common curl request method for request
     *
     * @param string    $type          HTTP verb: GET, POST, PUT, PATCH or DELETE.
     * @param string    $url           Request URL.
     * @param array     $data          Request data.
     * @param array     $headers       Headers data.
     * @param array     $curlOptions   cURL options.
     * @return void
     */
    private function request($type, $url, $data = [], $headers = [], $curlOptions = [])
    {
        $curl = curl_init();

        if (self::TYPE_GET === $type && count($data)) {
            $url = $url . '?' . http_build_query($data);
        }

        $options = array_replace($this->options, $curlOptions);

        if (in_array($type,[self::TYPE_POST,self::TYPE_PUT,self::TYPE_PATCH]) && count($data)) {
            $options[CURLOPT_POST] = 1;
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_CUSTOMREQUEST] = strtoupper($type);
        $options[CURLOPT_HTTPHEADER] = $headers;

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);

        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

        $this->statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $this->headers = substr($response, 0, $headerSize);
        $this->body = substr($response, $headerSize);

        curl_close($curl);

        return $this;
    }

    /**
     * GET request
     *
     * @param   string    $url
     * @param   array     $data
     * @param   array     $headers
     * @param   array     $curlOptions
     * @return  object
     */
    public function get($url, $data = [], $headers = [], $curlOptions = [])
    {
        return $this->request(self::TYPE_GET, $url, $data, $headers, $curlOptions);
    }

    /**
     * POST request
     *
     * @param   string  $url
     * @param   array   $data
     * @param   array   $headers
     * @param   array   $curlOptions
     * @return  object
     */
    public function post($url, $data = [], $headers = [], $curlOptions = [])
    {
        return $this->request(self::TYPE_POST, $url, $data, $headers, $curlOptions);
    }

    /**
     * PUT request
     *
     * @param   string  $url
     * @param   array   $data
     * @param   array   $headers
     * @param   array   $curlOptions
     * @return  object
     */
    public function put($url, $data = [], $headers = [], $curlOptions = [])
    {
        return $this->request(self::TYPE_PUT, $url, $data, $headers, $curlOptions);
    }

    /**
     * PATCH request
     *
     * @param   string  $url
     * @param   array   $data
     * @param   array   $headers
     * @param   array   $curlOptions
     * @return  object
     */
    public function patch($url, $data = [], $headers = [], $curlOptions = [])
    {
        return $this->request(self::TYPE_PATCH, $url, $data, $headers, $curlOptions);
    }

    /**
     * DELETE request
     *
     * @param   string  $url
     * @param   array   $headers
     * @param   array   $curlOptions
     * @return  object
     */
    public function delete($url, $headers = [], $curlOptions = [])
    {
        return $this->request(self::TYPE_DELETE, $url, [], $headers, $curlOptions);
    }

    /**
     * Get response status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Get response headers
     *
     * @return array
     */
    public function getHeaders()
    {
        $headers = array();
        $arrRequests = explode("\r\n\r\n", $this->headers);

        for ($index = 0; $index < count($arrRequests) - 1; $index++) {
            foreach (explode("\r\n", $arrRequests[$index]) as $i => $line) {
                if ($i === 0)
                    $headers[$index]['http_code'] = $line;
                else {
                    list($key, $value) = explode(': ', $line);
                    $headers[$index][$key] = $value;
                }
            }
        }

        return isset($headers[0]) ? $headers[0] : null;
    }

    /**
     * Get response header's specific key's value
     *
     * @param string $key
     * @return mixed
     */
    public function getHeader($key)
    {
        $headers = $this->getHeaders();

        return is_array($headers) ? (isset($headers[$key]) ? $headers[$key] : FALSE) : NULL;
    }

    /**
     * Get response raw body
     *
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get response body as JSON array.
     *
     * @return array|mixed
     */
    public function getJson()
    {
        return json_decode($this->getBody(), true);
    }

    /**
     * Get response as standard PHP object
     *
     * @return object
     */
    public function getObject()
    {
        return json_decode($this->getBody());
    }
}

/* End of file HttpClient.php */