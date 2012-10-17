<?php

class VCS_RechnungPlus {

	function __construct() {
	}

	function render() {
		$content = '';
		$logList = $this->getLog();
		$lastCommit = array_shift($logList);
		$latestTime = $lastCommit->date;
		$secondLastCommit = array_shift($logList);
		$earliestTime = $secondLastCommit->date;
		$commitDuration = strtotime($latestTime) - strtotime($earliestTime);	// sec
		$commitDuration = number_format($commitDuration / 60 / 60, 2, '.', '');	// hr
		debug(array(
			'earliestTime' => $earliestTime,
			'latestTime' => $latestTime,
			'duration' => $commitDuration,
		));
		$cmd = 'hg diff --stat -r '.$secondLastCommit->rev.' -r '.$lastCommit->rev;
		//debug($cmd);
		$files = $this->getChangedFiles($cmd);
		debug('Files', $files);
		$minTime = min($files);
		$fileTimes = array_flip($files);
		$minFile = $fileTimes[$minTime];
		$duration = strtotime($latestTime) - $minTime;				// sec
		$duration = number_format($duration / 60 / 60, 2, '.', '');	// hr
		debug(array(
			'cmd' => $cmd,
			'minTime' => date('r', $minTime),
			'minFile' => $minFile,
			'duration between two last commits' => $commitDuration.' hours',
			'duration between oldest edit since previous and latest commit' => $duration.' hours',
		));
		$this->pushToRechnungPlus($lastCommit, $duration);
		return $content;
	}

	function getLog() {
		//$log = `hg log -limit 2`;
		$log = `hg log`;
		$log = str_replace("\r\n", "\n", $log); // http://stackoverflow.com/questions/3997336/explode-php-string-by-new-line
		$log = str_replace("\n", "<br />", $log);
		$lines = trimExplode("<br />", $log, 999, false);
		//$lines = explode("<br />", $log);
		//$lines[] = '';  // to force creating a new LogItem in the end
		//debug('Lines', $lines);
		$collected = array();
		$logList = array();
		foreach ($lines as $line) {
			if (!$line) {
				$li = LogItem::parseLines($collected);
				if ($li->date) {	// parse OK
					$logList[$li->rev] = $li;
				}
				$collected = array();
			} else {
				$collected[] = $line;
			}
		}
		//debug('logList', $logList);
		return $logList;
	}

	function getChangedFiles($cmd) {
		$files = array();
		$result = `$cmd`;
		//debug($result);
		$result = trimExplode("\n", $result);
		foreach ($result as $line) {
			list($filename) = trimExplode('|', $line);
			if (file_exists($filename)) {
				$files[$filename] = filemtime($filename);
			}
		}
		return $files;
	}

	function pushToRechnungPlus(LogItem $li, $duration) {
		$da = new DigestAuth('Rechnung+ API');
		$da->userAssoc = $this->getUserAssoc();
		$da->run();
	}

}
