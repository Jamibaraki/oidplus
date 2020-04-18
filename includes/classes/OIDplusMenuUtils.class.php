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

class OIDplusMenuUtils {

	public static function nonjs_menu() {
		$json = array();

		$static_node_id = isset($_REQUEST['goto']) ? $_REQUEST['goto'] : 'oidplus:system';

		foreach (OIDplus::getPagePlugins('public') as $plugin) {
			$plugin->tree($json, null, true, $static_node_id);
		}

		foreach ($json as $x) {
			if ($static_node_id == $x['id']) echo '<b>';
			if (isset($x['indent'])) echo str_repeat('&nbsp', $x['indent']*5);
			echo '<a href="?goto='.urlencode($x['id']).'">';
			if (!empty($x['icon'])) echo '<img src="'.$x['icon'].'" alt=""> ';
			echo htmlentities($x['text']).'</a><br>';
			if ($static_node_id == $x['id']) echo '</b>';
		}

	}

	// req_id comes from jsTree via AJAX
	// req_goto comes from the user (GET argument)
	public static function json_tree($req_id, $req_goto) {
		$json = array();

		if (!isset($req_id) || ($req_id == '#')) {
			// 'ra' and 'admin' pages will not be iterated, because they usually have no tree icon, or an icon underneath the login section
			foreach (OIDplus::getPagePlugins('public') as $plugin) {
				$plugin->tree($json, null, false, $req_goto);
			}
		} else {
			$json = self::tree_populate($req_id);
		}

		return json_encode($json);
	}

	public static function tree_populate($parent, $goto_path=null) {
		$children = array();

		$parentObj = OIDplusObject::parse($parent);

		@list($namespace, $oid) = explode(':', $parent, 2);
		if ($namespace == 'oid') $oid = substr($oid, 1); // f�hrenden Punkt entfernen

		if (!is_null($goto_path)) array_shift($goto_path);

		$confidential_oids = array();

		$res = OIDplus::db()->query("select id from ###objects where confidential = '1'");
		while ($row = $res->fetch_array()) {
			$confidential_oids[] = $row['id'];
		}

		$res = OIDplus::db()->query("select * from ###objects where parent = ? order by ".OIDplus::db()->natOrder('id'), array($parent));
		while ($row = $res->fetch_array()) {
			$obj = OIDplusObject::parse($row['id']);

			if (!$obj->userHasReadRights()) continue;

			$child = array();
			$child['id'] = $row['id'];

			// Anzeigenamen (relative OID) bestimmen
			$child['text'] = $obj->jsTreeNodeName($parentObj);
			$child['text'] .= empty($row['title']) ? /*' -- <i>'.htmlentities('Title missing').'</i>'*/ '' : ' -- <b>' . htmlentities($row['title']) . '</b>';

			$is_confidential = false;
			foreach ($confidential_oids as $test) {
				$is_confidential |= ($row['id'] === $test) || (strpos($row['id'],$test.'.') === 0);
			}
			if ($is_confidential) {
				$child['text'] = '<font color="gray"><i>'.$child['text'].'</i></font>';
			}

			// Icon bestimmen
			$child['icon'] = $obj->getIcon($row);

			// Feststellen, ob es weitere Unter-OIDs gibt
			if (!is_null($goto_path) && (count($goto_path) > 0) && ($goto_path[0] === $row['id'])) {
				$child['children'] = self::tree_populate($row['id'], $goto_path);
				$child['state'] = array("opened" => true);
			} else {
				$obj_children = $obj->getChildren();

				// Variant 1: Fast, but does not check for hidden OIDs
				//$child_count = count($obj_children);

				// variant 2
				$child_count = 0;
				foreach ($obj_children as $obj_test) {
					if (!$obj_test->userHasReadRights()) continue;
					$child_count++;
				}

				$child['children'] = $child_count > 0;
			}

			$children[] = $child;
		}

		return $children;
	}
}