<?php

/*
 * OIDplus 2.0
 * Copyright 2020 Daniel Marschall, ViaThinkSoft
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

# More information about the OAuth2 implementation:
# - https://developers.facebook.com/docs/facebook-login/manually-build-a-login-flow
# - https://developers.facebook.com/tools/explorer/

require_once __DIR__ . '/../../../includes/oidplus.inc.php';

OIDplus::init(true);

if (!OIDplus::baseConfig()->getValue('FACEBOOK_OAUTH2_ENABLED', false)) {
	throw new OIDplusException(_L('Facebook OAuth authentication is disabled on this system.'));
}

if (!isset($_GET['code'])) die();
if (!isset($_GET['state'])) die();

if ($_GET['state'] != $_COOKIE['csrf_token']) {
	die('Invalid CSRF token');
}

// Get access token

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"https://graph.facebook.com/v8.0/oauth/access_token?".
	"client_id=".urlencode(OIDplus::baseConfig()->getValue('FACEBOOK_OAUTH2_CLIENT_ID'))."&".
	"redirect_uri=".urlencode(OIDplus::getSystemUrl(false).OIDplus::webpath(__DIR__).'oauth.php')."&".
	"client_secret=".urlencode(OIDplus::baseConfig()->getValue('FACEBOOK_OAUTH2_CLIENT_SECRET'))."&".
	"code=".$_GET['code']
);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$cont = curl_exec($ch);
curl_close($ch);
$data = json_decode($cont,true);
if (isset($data['error'])) {
	echo '<h2>Error at step 2</h2>';
	echo '<p>'.$data['error']['message'].'</p>';
	die();
}
$access_token = $data['access_token'];

// Get user infos

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"https://graph.facebook.com/v8.0/me?".
	"fields=id,email,name&".
	"access_token=".urlencode($access_token)
);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$cont = curl_exec($ch);
curl_close($ch);
$data = json_decode($cont,true);
if (isset($data['error'])) {
	// TODO: OIDplus design, multilang
	echo '<h2>Error at step 2</h2>';
	echo '<p>'.$data['error']['message'].'</p>';
	die();
}
$personal_name = $data['name'];
$email = !isset($data['email']) ? '' : $data['email'];
if (empty($email)) {
	echo '<h2>Facebook Login</h2>';
	echo '<p>Your Facebook account does not have an email address.</p>';
	die();
}

// Everything's done! Now login and/or create account

if (!empty($email)) {
	$ra = new OIDplusRA($email);
	if (!$ra->existing()) {
		$ra->register_ra(null); // create a user account without password

		OIDplus::db()->query("update ###ra set ra_name = ?, personal_name = ? where email = ?", array($personal_name, $personal_name, $email));

		OIDplus::logger()->log("[INFO]RA($email)!", "RA '$email' was created because of successful Facebook OAuth2 login");
	}

	OIDplus::logger()->log("[OK]RA($email)!", "RA '$email' logged in via Facebook OAuth2");
	OIDplus::authUtils()::raLogin($email);

	OIDplus::db()->query("UPDATE ###ra set last_login = ".OIDplus::db()->sqlDate()." where email = ?", array($email));

	// Go back to OIDplus

	header('Location:'.OIDplus::getSystemUrl(false));
}
