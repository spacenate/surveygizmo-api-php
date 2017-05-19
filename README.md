# SurveyGizmoApiWrapper
PHP wrapper for the SurveyGizmo REST API

## Installing with Composer:

Add these contents to your `composer.json` file, and then run `composer install`.

    {
        "repositories": [
            {
                "type": "git",
                "url": "https://github.com/spacenate/surveygizmo-api-php"
            }
        ],
        "require": {
            "spacenate/surveygizmo-api-php": "dev-master"
        }
    }

## A simple example

    <?php
    use spacenate\SurveyGizmoApiWrapper;

    require 'vendor/autoload.php';

    $sg = new SurveyGizmoApiWrapper($api_token = "123ABC789DEF0000123ABC789DEF0000", $secret = "meow");

    if (!$sg->testCredentials()) {
        die("Poop! Failed to authenticate with the provided credentials.");
    }

    $filter = array(
        array("date_modified", ">", "2015-01-01"),
        array("status", "=", "Launched")
    );

    $surveys = json_decode($sg->Survey->getList($page = 1, $limit = 10, $filter));
    print_r($surveys);

## Using OAuth

To use OAuth, you'll first need to provide an OAuth key, secret, and callback URL.

    <?php
    use spacenate\SurveyGizmoApiWrapper();

    require 'vendor/autoload.php';

    // Create wrapper object
    $sg = new SurveyGizmoApiWrapper();

    // Consumer key and secret obtained via SurveyGizmo OAuth Application Registration form
    // Visit https://app.surveygizmo.com/account/restful-register while logged in to SurveyGizmo
    $oauth_config = array(
        'consumer_key'    => 'aaaa0000aaaa0000aaaa0000',
        'consumer_secret' => 'bbbbb1111bbbb11111bbb1111',
        'oauth_callback'  => 'https://example.com'
    );

    // Use OAuth object's configure() method to add OAuth configuration
    $sg->oauth->configure($oauth_config);

To obtain an access token, begin by getting a request token and directing the user to SurveyGizmo's Authorize page.

    // Get a request token from SurveyGizmo -- Note that this is returned as an associative array!
    $result = $sg->oauth->getRequestToken();

    // Redirect User to SurveyGizmo Authorize page
    if (isset($result["oauth_token"])) {
        header("Location: https://restapi.surveygizmo.com/head/oauth/authenticate?oauth_token=" . $result["oauth_token"]);
        die;
    } else {
        die("Uh oh! Failed to get a request token from SurveyGizmo. Check your consumer key and secret.");
    }

Once the user has authorized access, they will be sent to your callback URL with an `oauth_token` and `oauth_verifier`, which you can trade for an access token and secret.

    // Grab parameters included when User is sent to callback URL
    $oauth_token = $_GET['oauth_token'];
    $oauth_verifier= $_GET['oauth_verifier'];

    // Exchange for access token and secret -- Note that this is returned as an associative array!
    $result = $sg->oauth->getAccessToken($oauth_token, $oauth_verifier);

    // Yay credentials! Note that at this time, SurveyGizmo OAuth tokens *CANNOT* be revoked, so store these properly encrypted in a safe place
    $access_token = $result['oauth_token'];
    $access_token_secret = $result['oauth_token_secret'];

To use an OAuth access token, use the `setCredentials()` method, specifying the `"oauth"` type.

    $sg->setCredentials($access_token, $access_token_secret, $type = "oauth");

### SurveyGizmo API Documentation
[http://apihelp.surveygizmo.com/help](http://apihelp.surveygizmo.com/help "SurveyGizmo REST API Help Documentation")

### License

MIT
