<?php

function trimExplode($sep, $str, $limit = 999999, $removeEmpty = true) {
	$parts = explode($sep, $str, $limit);
	//debug('Parts', array($str, $sep, $parts)); exit();
	$parts = array_map('trim', $parts);
	if ($removeEmpty) {
		$parts = array_filter($parts);
		$parts = array_values($parts);
	}
	return $parts;
}

function debug($name, array $array = array()) {
	if (!is_string($name)) {
		$array = $name;
	} else {
		echo $name.': ';
	}
	print_r($array);
	echo "\n";
}
