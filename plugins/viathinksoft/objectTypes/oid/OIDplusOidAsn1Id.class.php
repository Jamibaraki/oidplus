<?php

/*
 * OIDplus 2.0
 * Copyright 2019 - 2022 Daniel Marschall, ViaThinkSoft
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

namespace ViaThinkSoft\OIDplus;

class OIDplusOidAsn1Id {
	private $name = '';
	private $standardized = false;
	private $well_known = false;
	function __construct($name, $standardized, $well_known) {
		$this->name = $name;
		$this->standardized = $standardized;
		$this->well_known = $well_known;
	}
	function getName() {
		return $this->name;
	}
	function isStandardized() {
		return $this->standardized;
	}
	function isWellKnown() {
		return $this->well_known;
	}
	function __toString() {
		return $this->name;
	}
}
