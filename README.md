# SurveyGizmoApiWrapper
PHP wrapper for the SurveyGizmo REST API

## Installing with Composer:

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

    use spacenate\SurveyGizmoApiWrapper;

    $sg = new SurveyGizmoApiWrapper($userId = "email@address.com", $secret = "5ebe2294ecd0e0f08eab7690d2a6ee69", $type = "md5");

    if (!$sg->testCredentials()) {
        die("Poop! Failed to authenticate with the provided credentials.");
    }

    $filter = array(
        array("modifiedon", ">", "2015-01-01"),
        array("status", "=", "Launched")
    );

    $surveys = json_decode($sg->Survey->getList($page = 1, $limit = 10, $filter));
    print_r($surveys);

## Using OAuth

To use OAuth, you'll first need to provide an OAuth key, secret, and callback URL.

    use spacenate\SurveyGizmoApiWrapper();

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

    // Yay credentials! Note that at this time, SurveyGizmo OAuth tokens *CANNOT* be revoked, so store these in a very safe place
    $access_token = $result['oauth_token'];
    $access_token_secret = $result['oauth_token_secret'];

To use an OAuth access token, use the `setCredentials()` method, specifying the `"oauth"` type.

    $sg->setCredentials($access_token, $access_token_secret, $type = "oauth");

### SurveyGizmo API Documentation
[http://apihelp.surveygizmo.com/help](http://apihelp.surveygizmo.com/help "SurveyGizmo REST API Help Documentation")

### License

MIT
