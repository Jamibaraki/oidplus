<?php

/*
 * OIDplus 2.0
 * Copyright 2019 Daniel Marschall, ViaThinkSoft
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

if (!defined('IN_OIDPLUS')) die();

class OIDplusRegistrationWizard extends OIDplusPagePlugin {
	public function type() {
		return 'system';
	}

	public function priority() {
		return 000;
	}

	public function action(&$handled) {
		if (isset($_REQUEST["action"]) && ($_REQUEST['action'] == "vts_regqry")) {
			$handled = true;

			// This is what we answer to the ViaThinkSoft server
			if (function_exists('openssl_sign')) {
				$payload = array(
					"version" => 1,
					"vts_directory_listing" => OIDplus::config()->getValue('reg_enabled') ? true : false,
					"oidinfo_xml_unlocked" => OIDplus::config()->exists('oidinfo_export_protected') && !OIDplus::config()->getValue('oidinfo_export_protected') ? true : false,
					"oidinfo_xml_location" => 'plugins/adminPages/400_oidinfo_export/oidinfo_export.php?online=1'
				);

				$signature = '';
				openssl_sign(json_encode($payload), $signature, OIDplus::config()->getValue('oidplus_private_key'));

				$data = array(
					"payload" => $payload,
					"signature" => base64_encode($signature)
				);
			} else {
				$data = array(
					"error" => "OpenSSL not available"
				);
			}
			echo json_encode($data);
		}
	}

	public function cfgSetValue($name, $value) {
		if ($name == 'reg_enabled') {
			if (($value != '0') && ($value != '1')) {
				throw new Exception("Please enter either 0 or 1.");
			}
		}
	}

	public function gui($id, &$out, &$handled) {
		// nothing
	}

	public function tree(&$json, $ra_email=null, $nonjs=false, $req_goto='') {
		return false;
	}

	public function init($html=true) {
		OIDplus::config()->prepareConfigKey('registration_done', 'Registration wizard done once?', '0', 1, 0);
		OIDplus::config()->prepareConfigKey('reg_enabled', 'Register your system to the ViaThinkSoft directory?', '0', 0, 1);
		OIDplus::config()->prepareConfigKey('reg_ping_interval', 'Registration ping interval', '3600', 0, 0);
		OIDplus::config()->prepareConfigKey('reg_last_ping', 'Last ping to ViaThinkSoft directory services', '0', 1, 0);

		if (function_exists('openssl_sign')) {
			// Show registration wizard once

			if ($html && (OIDplus::config()->getValue('registration_done') != '1')) {
				if (basename($_SERVER['SCRIPT_NAME']) != 'registration.php') {
					header('Location:plugins/system/'.basename(__DIR__).'/registration.php');
					die();
				}
			}

			// Is it time to register / renew directory entry?

			if ((OIDplus::config()->getValue('reg_enabled')) &&
			   (time()-OIDplus::config()->getValue('reg_last_ping') >= OIDplus::config()->getValue('reg_ping_interval'))) {
				if ($system_url = OIDplus::system_url()) {
					$payload = array(
						"system_id" => OIDplus::system_id(false),
						"public_key" => OIDplus::config()->getValue('oidplus_public_key'),
						"system_url" => $system_url,
						"hide_system_url" => 0,
						"hide_public_key" => 0,
						"admin_email" => OIDplus::config()->getValue('admin_email'),
						"system_title" => OIDplus::config()->systemTitle()
					);

					$signature = '';
					openssl_sign(json_encode($payload), $signature, OIDplus::config()->getValue('oidplus_private_key'));

					$data = array(
						"payload" => $payload,
						"signature" => base64_encode($signature)
					);

					$res = file_get_contents('https://oidplus.viathinksoft.com/reg/register.php?data='.base64_encode(json_encode($data)));
					// die("RES: $res\n");
					// if ($res == 'OK') ...

					OIDplus::config()->setValue('reg_last_ping', time());
				}
			}
		}
	}

	public function tree_search($request) {
		return false;
	}
}

OIDplus::registerPagePlugin(new OIDplusRegistrationWizard());