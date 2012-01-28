# OAuth consumer class for CakePHP

## Purpose

An OAuth consumer class for CakePHP 1.x supporting OAuth 1.0 as defined in http://tools.ietf.org/html/rfc5849.

## Installation

* Copy the `vendors/OAuth` folder to the `vendors` folder of your application

## Usage

To use the OAuth client class, you have to import it with `App::import()`.

Before you can instantiate the consumer class, you have to register your application with your API provider to get consumer key and consumer secret (for this example you have to register your application at https://twitter.com/oauth). Consumer key and consumer secret are required as parameters for the constructor. In the example below I moved the instantiation of the consumer class to a private method `createConsumer()` to avoid code duplication.

In the `index` method a request token is obtained and the user is redirected to Twitter where he has to authorize the request token.

In the `callback` method the request token is exchanged for an access token. Using this access token, a new status is posted to Twitter. Please note that in a real application, you would save the access token data in a database to avoid that the user has to go through the process of getting an access token over and over again.

```php
// controllers/twitter_controller.php
App::import('Vendor', 'oauth', array('file' => 'OAuth' . DS . 'oauth_consumer.php'));

class TwitterController extends AppController {
  public $uses = array();

  public function index() {
    $consumer = $this->createConsumer();
    $requestToken = $consumer->getRequestToken('https://api.twitter.com/oauth/request_token', 'http://' . $_SERVER['HTTP_HOST'] . '/twitter/callback');

    if ($requestToken) {
      $this->Session->write('twitter_request_token', $requestToken);
      $this->redirect('https://api.twitter.com/oauth/authorize?oauth_token=' . $requestToken->key);
    } else {
      // an error occured when obtaining a request token
    }
  }

  public function callback() {
    $requestToken = $this->Session->read('twitter_request_token');
    $consumer = $this->createConsumer();
    $accessToken = $consumer->getAccessToken('https://api.twitter.com/oauth/access_token', $requestToken);

    if ($accessToken) {
      $consumer->post($accessToken->key, $accessToken->secret, 'https://api.twitter.com/1/statuses/update.json', array('status' => 'hello world!'));
    }
    exit;
  }

  private function createConsumer() {
    return new OAuth_Consumer('YOUR_CONSUMER_KEY', 'YOUR_CONSUMER_SECRET');
  }
}
```

## Contact

If you have questions or feedback, feel free to contact me via Twitter ([@dhofstet](https://twitter.com/dhofstet)) or by email (daniel.hofstetter@42dh.com).

## License

The OAuth consumer class is licensed under the MIT license.
