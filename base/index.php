<?php

/*

	uNetBTRON https://github.com/paijp/unet-btron
	
	Copyright (c) 2023 paijp

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see <https://www.gnu.org/licenses/>.

*/

include_once("env.php");

if (@$sys_utadpath === null)
	$sys_utadpath = "/var/www/db/utad0/";


if (!function_exists("hex2bin")) {
	function	hex2bin($s)
	{
		$hex2bin = "0123456789abcdef";
		$s = strtolower($s);
		$ret = "";
		$pos = 0;
		while ($pos < strlen($s)) {
			$code = strpos($hex2bin, substr($s, $pos++, 1)) << 4;
			$code |= strpos($hex2bin, substr($s, $pos++, 1));
			$ret .= chr($code);
		}
		return $ret;
	}
}


if (preg_match('#^([0-9A-Fa-f]+)/r([0-9A-Fa-f]{8})$#', $datapath = @$_GET["data"])) {
	$fn = "{$sys_utadpath}/data/{$datapath}.utad";
	switch (@$_GET["cmd"]) {
		case	"r":
			$fp0 = fopen($fn, "r") or die();
			$output = array();
			while (($line = fgets($fp0)) !== FALSE) {
				$a = explode("?", trim($line), 2);
				if ($a[0] == "")
					continue;
				$a0 = explode("&", @$a[1]);
				$list = array("@" => $a[0]);
				foreach ($a0 as $s) {
					if (!preg_match('/^([_0-9A-Za-z]+)=(.*)$/', $s, $a1))
						continue;
					$list[$a1[1]] = urldecode($a1[2]);
				}
				ksort($list);
				$output[] = $list;
			}
			header("Content-Type: application/json");
			print json_encode($output);
			die();
		case	"w":
			header("Content-Type: text/plain");
			$content = file_get_contents("php://input");
			$list = json_decode($content, true);
			$output = "";
			foreach ($list as $obj) {
				ksort($obj);
				$output .= $obj["@"];
				$first = 1;
				foreach ($obj as $k => $v) {
					if ($k == "@")
						continue;
					if (($first)) {
						$first = 0;
						$output .= "?";
					} else
						$output .= "&";
					$output .= urlencode($k)."=".urlencode($v);
				}
				$output .= "\n";
			}
			file_put_contents($fn, $output);
			die();
	}
	if (!is_file($fn))
		die("is not file({$fn}).");
	if (!is_readable($fn))
		die("file not readable({$fn}).");
	if (!is_writable($fn))
		die("file not writable({$fn}).");
	$applfn = "{$sys_utadpath}/appl/68747470733a2f2f6769746875622e636f6d2f7061696a702f756e65742d6274726f6e2e676974/fged.html";
	if (preg_match('#^([0-9A-Fa-f]+)/([-_0-9A-Fa-f]+)$#', $applpath = @$_GET["appl"])) {
		$fn = "{$sys_utadpath}/appl/{$applpath}.html";
		if (!is_file($fn))
			;
		else if (!is_readable($fn))
			;
		else
			$applfn = $fn;
	}
	print file_get_contents($applfn);
	die();
}


$fp0 = popen("cd {$sys_utadpath};ls -1 data/*/r00000000.utad", "r") or die("popen failed.");
while (($line = fgets($fp0)) !== FALSE) {
	$line = trim($line);
	if (!preg_match('#^data/(([0-9A-Fa-f]+)/r00000000)[.]utad$#', $line, $a))
		continue;
	$s = $a[1];
	$h = htmlspecialchars(hex2bin($a[2]));
	print <<<EOO
<a href="?data={$s}" target="{$s}">{$h}</a><br />

EOO;
}

