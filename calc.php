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

	$calc = new Calc;
	$calc->fieldsCount = $fieldsCount;
	$calc->chipCount = $chipCount;
	$calc->run();
	$calc->write();
	$calc->show_result();

}

class Calc {

	public $start;
	public $fieldsCount;
	public $chipCount;
	public $arr = array();
	public $res = array();

	public function run() {
		$this->start = microtime(true);
		$this->res[] = $this->get($this->fieldsCount) / ($this->get($this->chipCount) * $this->get($this->fieldsCount - $this->chipCount));
	}

	public function get($x) {
		return $x ? $x * $this->get($x - 1) : 1;
	}

	public function write() {
		$fd = fopen('result.txt', 'a+b');
		if (count($this->res) < 10) {
			fwrite($fd, "менее 10 вариантов\r\n");
		} else {
			fwrite($fd, count($this->res) . "\r\n");
			for ($i = 0; $i < count($this->res); $i++) {
				$r = implode('', $this->res[$i]);
				fwrite($fd, $r . "\r\n");
			}
		}
		fclose($fd);
	}

	public function show_result() {
		echo 'The file is ready!<br /><br />';
		echo 'Peak requested: ' . (int)(memory_get_peak_usage() / 1024) . ' KB';
		echo '<br />';
		echo 'Peak allocated: ' . (int)(memory_get_peak_usage(true) / 1024) . ' KB';

		$time = microtime(true) - $this->start;
		echo 'Time: ' . gmdate("H:i:s", $time);
	}
}

