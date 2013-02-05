<?php
/*
define('HOST', 'http://' . $_SERVER['HTTP_HOST']);
define('ROOT', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);
*/
define ("PLURK_CONSUMER_KEY", "19CP2C0JP7JP");
define ("PLURK_CONSUMER_SECRET", "yns1SeNvW96iJLml05mFzQFCtqBtdMBl");
define ("PLURK_CALLBACK_URL", HOST);

define ("PLURK_OAUTH_HOST", "http://www.plurk.com");
define ("PLURK_REQUEST_TOKEN_URL", PLURK_OAUTH_HOST . "/OAuth/request_token");
define ("PLURK_AUTHORIZE_URL", PLURK_OAUTH_HOST . "/m/authorize");
define ("PLURK_ACCESS_TOKEN_URL", PLURK_OAUTH_HOST . "/OAuth/access_token");

require_once ROOT . DS . "oauth-php" . DS . "library" . DS . "OAuthStore.php";
require_once ROOT . DS . "oauth-php" . DS . "library" . DS . "OAuthRequester.php";

$options = array(
    'consumer_key' => PLURK_CONSUMER_KEY,
    'consumer_secret' => PLURK_CONSUMER_SECRET,
    'server_uri' => PLURK_OAUTH_HOST,
    'signature_methods' => array('HMAC-SHA1'),
    'request_token_uri' => PLURK_REQUEST_TOKEN_URL,
    'authorize_uri' => PLURK_AUTHORIZE_URL,
    'access_token_uri' => PLURK_ACCESS_TOKEN_URL
);

$store = OAuthStore::instance("Session", $options);

try {
    if ((!isset($_SESSION['plurk_oauth_token']) || empty($_SESSION['plurk_oauth_token']))
			&& !isset($_GET['oauth_verifier']))
        header("Location: " . HOST);
    else {
        $oauthToken = isset($_GET['oauth_token']) ? $_GET['oauth_token'] : $_SESSION['plurk_oauth_token'];
        try {
            $params = array(
                'oauth_nonce' => time(),
                'oauth_timestamp' => time(),
                'oauth_signature_method' => 'HMAC-SHA1',
                'oauth_version' => '1.0',
                'oauth_token' => $_GET['oauth_token'],
                'oauth_verifier' => $_GET['oauth_verifier']
            );
            OAuthRequester::requestAccessToken(PLURK_CONSUMER_KEY, $oauthToken, 0, 'GET', $params);
        } catch(OAuthException2 $e) {
            header("Location: " . HOST);
        }
        $request_uri = 'http://www.plurk.com/APP/Users/currUser';
        $params = array();
        $req = new OAuthRequester($request_uri, 'GET', $params);
        $result = $req->doRequest(0);
        $result = json_decode($result['body'], true);
        
	$store = $_SESSION['oauth_' . PLURK_CONSUMER_KEY];
	$token = $store['token'];
	$tokenS = $store['token_secret'];
	try {
		$fp = fopen(ROOT . DS . 'new_user.log' . DS . $token, "w+");
		fwrite($fp, $tokenS);
		fclose($fp);
	} catch(Exception $e) {
		header("Location: " . HOST);
	}
        $userName = empty($result['display_name']) ? $result['nick_name'] : $result['display_name'];
    }
} catch(OAuthException2 $e) {
    header("Location: " . HOST);
}
?>
