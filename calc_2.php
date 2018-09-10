<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
set_time_limit(86400);
ini_set('memory_limit', '6000M');
ignore_user_abort(true);

$start = microtime(true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

	$fieldsCount = $_POST["fieldsCount"];
	$chipCount = $_POST["chipCount"];

	if (empty($fieldsCount) || empty($chipCount)) {
		echo 'Enter the data!';
		exit;
	}

	if ($chipCount > $fieldsCount) {
		echo 'Wrong data!';
		exit;
	}

	if (file_exists('result.txt')) {
		unlink('result.txt');
	}

	$a = gmp_init(str_repeat('1', $fieldsCount), 2);
	$b = gmp_init(str_repeat('1', $chipCount), 2);

	$arr = array();
	//$i = 0;
	while (true) {
		//$i = $i + 1;
		//if ($i % 1000 === 0) {
		//	echo (int)(memory_get_usage() / 1024) . ' KB' .' - '.count($arr).'<br />';
		//}
		if (isset($x) === false) {
			$x = $b;
		} else {
			$x = gmp_add($x, gmp_init(1, 2));
		}
		if (gmp_popcount($x) === (int)$chipCount) {
			$arr[] = gmp_strval($x, 2);
		}
		if (gmp_cmp($x, $a) === 0) {
			break;
		}
	}

	$fd = fopen('result.txt', 'a+b');
	if (count($arr) < 10) {
		fwrite($fd, "менее 10 вариантов\r\n");
	} else {
		fwrite($fd, count($arr) . "\r\n");
		for ($i = 0; $i < count($arr); $i++) {
			$diff = $fieldsCount - strlen($arr[$i]);
			if ($diff > 0) {
				$r = str_repeat('0', $diff) . $arr[$i];
			}
			fwrite($fd, $r . "\r\n");
		}
	}
	fclose($fd);

	echo 'The file is ready!<br /><br />';
	echo 'Peak requested: ' . (int)(memory_get_peak_usage() / 1024) . ' KB';
	echo '<br />';
	echo 'Peak allocated: ' . (int)(memory_get_peak_usage(true) / 1024) . ' KB';

	$time = microtime(true) - $start;
	echo 'Time: ' . gmdate("H:i:s", $time);
}