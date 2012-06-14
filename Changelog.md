# Changelog

### v1.0.1 (2012-01-27)

* Updating the OAuth library

### v1.0.0 (2011-08-24)

* Switching to Semantic Versioning
* Re-purpose the `getFullResponse()` method so it will always return an array with the complete response of the last request

### v2009-09-05

* Including the PHP library for OAuth in the package for convenience purposes, so you no longer have to download this library separately
* Adding a `getFullResponse()` method for debugging purposes
* Adapting the class for OAuth 1.0a. The `getRequestToken()` method got a new parameter named `$callback`: `getRequestToken($requestTokenURL, $callback = 'oob', $httpMethod = 'POST', $parameters = array())`

### v2009-03-30

* Initial release
