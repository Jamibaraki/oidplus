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

class OIDplusRA {
	private $email = null;

	public function __construct($email) {
		$this->email = $email;
	}

	public function raEmail() {
		return $this->email;
	}

	public function raName() {
		$res = OIDplus::db()->query("select ra_name from ".OIDPLUS_TABLENAME_PREFIX."ra where email = '".OIDplus::db()->real_escape_string($this->email)."'");
		if (OIDplus::db()->num_rows($res) == 0) return "(RA not in database)";
		$row = OIDplus::db()->fetch_array($res);
		return $row['ra_name'];
	}

	public static function getAllRAs() {
		$out = array();
		$res = OIDplus::db()->query("select email from ".OIDPLUS_TABLENAME_PREFIX."ra");
		while ($row = OIDplus::db()->fetch_array($res)) {
			$out[] = new OIDplusRA($row['email']);
		}
		return $out;
	}

	public function change_password($new_password) {
		$s_salt = substr(md5(rand()), 0, 7);
		$calc_authkey = 'A2#'.base64_encode(version_compare(PHP_VERSION, '7.1.0') >= 0 ? hash('sha3-512', $s_salt.$new_password, true) : bb\Sha3\Sha3::hash($s_salt.$new_password, 512, true));
		if (!OIDplus::db()->query("update ".OIDPLUS_TABLENAME_PREFIX."ra set salt='".OIDplus::db()->real_escape_string($s_salt)."', authkey='".OIDplus::db()->real_escape_string($calc_authkey)."' where email = '".OIDplus::db()->real_escape_string($this->email)."'")) {
			throw new Exception(OIDplus::db()->error());
		}
	}

	public function change_email($new_email) {
		if (!OIDplus::db()->query("update ".OIDPLUS_TABLENAME_PREFIX."ra set email = '".OIDplus::db()->real_escape_string($new_email)."' where email = '".OIDplus::db()->real_escape_string($this->email)."'")) {
			throw new Exception(OIDplus::db()->error());
		}
	}

	public function register_ra($new_password) {
		$s_salt = substr(md5(rand()), 0, 7);
		$calc_authkey = 'A2#'.base64_encode(version_compare(PHP_VERSION, '7.1.0') >= 0 ? hash('sha3-512', $s_salt.$new_password, true) : bb\Sha3\Sha3::hash($s_salt.$new_password, 512, true));
		if (!OIDplus::db()->query("insert into ".OIDPLUS_TABLENAME_PREFIX."ra (salt, authkey, email, registered) values ('".OIDplus::db()->real_escape_string($s_salt)."', '".OIDplus::db()->real_escape_string($calc_authkey)."', '".OIDplus::db()->real_escape_string($this->email)."', now())")) {
			throw new Exception(OIDplus::db()->error());
		}
	}

	public function checkPassword($password) {
		$ra_res = OIDplus::db()->query("select * from ".OIDPLUS_TABLENAME_PREFIX."ra where email = '".OIDplus::db()->real_escape_string($this->email)."'");
		$ra_row = OIDplus::db()->fetch_array($ra_res);
		$s_salt = $ra_row['salt'];
		@list($s_authmethod, $s_authkey) = explode('#', $ra_row['authkey'], 2);

		if ($s_authmethod == 'A1') {
			// Downwards compatibility for ViaThinkSoft FreeOID
			$calc_authkey = sha1('asdlkgfdklgnklsdlkans'.$s_salt.$password);
		} else if ($s_authmethod == 'A2') {
			$calc_authkey = base64_encode(version_compare(PHP_VERSION, '7.1.0') >= 0 ? hash('sha3-512', $s_salt.$password, true) : bb\Sha3\Sha3::hash($s_salt.$password, 512, true));
		} else {
			// Invalid auth code
			return false;
		}

		return hash_equals($calc_authkey, $s_authkey);
	}

	public function delete() {
		if (!OIDplus::db()->query("delete from ".OIDPLUS_TABLENAME_PREFIX."ra where email = '".OIDplus::db()->real_escape_string($this->email)."'")) {
			throw new Exception(OIDplus::db()->error());
		}
	}

	public function setRaName($ra_name) {
		if (!OIDplus::db()->query("update ".OIDPLUS_TABLENAME_PREFIX."ra set ra_name = '".OIDplus::db()->real_escape_string($ra_name)."' where email = '".OIDplus::db()->real_escape_string($this->email)."'")) {
			throw new Exception(OIDplus::db()->error());
		}
	}
}
