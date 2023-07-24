# CodeIgniter HTTP Client

Load library:
```php
$this->load->library('http');
```
... or with configs:
```php
$this->load->library('http',[
  CURLOPT_VERBOSE => 1
]);
```

## How to use
### GET
```php
$url    = "https://jsonplaceholder.typicode.com/comments";
$data   = [ 'postId' => 1 ];

$this->http->get($url,$data);
```
### POST
```php
$url    = "https://jsonplaceholder.typicode.com/posts";
$data   = [ 'title' => 'This is post title' ];

$this->http->post($url,$data);
```

### More options
```php
$response = $this->http->get($url, $queryString, $headers, $curlOptions);
$response = $this->http->post($url, $jsonData, $headers, $curlOptions);
$response = $this->http->put($url, $jsonData, $headers, $curlOptions);
$response = $this->http->patch($url, $jsonData, $headers, $curlOptions);
$response = $this->http->delete($url, $headers, $curlOptions);
```

## Authentication
* _Basic_
```php
$response = $this->http->get("http://url/backend",["server" => "8000"],[],[CURLOPT_USERPWD => "admin:admin"]);
```
* _Digest_
```php
$response = $this->http->get("http://url/backend",["server" => "8000"],[],[
  CURLOPT_USERPWD => "admin:admin",
  CURLOPT_HTTPAUTH, CURLAUTH_DIGEST]);
```

You can check [other authentication options](https://curl.se/libcurl/c/CURLOPT_HTTPAUTH.html).

## Useful methods
```php
$response->getStatusCode(); // to get response code
$response->getHeaders(); // to get all headers as array
$response->getHeader($name); // to get specific header
$response->getBody(); // to get raw response body
$response->getJson(); // to get body as assoc array
$response->getObject(); // to get body as object
```