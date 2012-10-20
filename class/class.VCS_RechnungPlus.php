<?php

class VCS_RechnungPlus {

	function render() {
		$content = '';
		$logList = $this->getLog();
		if (sizeof($logList) > 1) {
			$lastCommit = array_shift($logList);
			$secondLastCommit = array_shift($logList);
			$commitDuration = strtotime($lastCommit->date) - strtotime($secondLastCommit->date);	// sec
			$commitDuration = number_format($commitDuration / 60 / 60, 2, '.', '');	// hr
			debug(array(
				'earliestTime' => $secondLastCommit->date,
				'latestTime' => $lastCommit->date,
				'duration' => $commitDuration,
			));
			$cmd = 'hg diff --stat -r '.$secondLastCommit->rev.' -r '.$lastCommit->rev;
			//debug($cmd);
			$files = $this->getChangedFiles($cmd);
			debug('Files', $files);
			$minTime = min($files);
			$fileTimes = array_flip($files);
			$minFile = $fileTimes[$minTime];
			$duration = strtotime($lastCommit->date) - $minTime;				// sec
			$duration = number_format($duration / 60 / 60, 2, '.', '');	// hr
			debug(array(
				'cmd' => $cmd,
				'minTime' => date('r', $minTime),
				'minFile' => $minFile,
				'duration between two last commits' => $commitDuration.' hours',
				'duration between oldest edit since previous and latest commit' => $duration.' hours',
			));
			if ($duration > 0) {
				if ($duration < 8) {
					$this->pushToRechnungPlus($lastCommit, $minTime, strtotime($lastCommit->date));
				} else {
					throw new Exception('Duration ('.$duration.') is unrealisticly too large.');
				}
			} else {
				throw new Exception('Negative duration indicates you didn\'t commit.');
			}
		} else {
			throw new Exception('Need to have at least two revisions.');
		}
		return $content;
	}

	function getLog() {
		//$log = `hg log -limit 2`;
		$log = `hg log -v`;
		$log = str_replace("\r\n", "\n", $log); // http://stackoverflow.com/questions/3997336/explode-php-string-by-new-line
		$log = str_replace("\n\n", "\n", $log);
		$lines = trimExplode("\n", $log, 999, false);
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

	function pushToRechnungPlus(LogItem $li, $from, $till) {
		$from = date('Ymd\THisO', $from);
		$till = date('Ymd\THisO', $till);
		$content = array(
			array(
				"recordId" => $li->changeset,
				"startDate" => $from,
				"endDate" => $till,
				'note' => $li->description,
				"tags" => array(basename(getcwd()).''),
    		),
		);
		//debug($content);
		$content = 'jsonData='.urlencode(json_encode($content));	// why urlencode: http://stackoverflow.com/a/5224895/417153
		//echo $content."\n";

		$ini = parse_ini_file('.hg/hgrc', true);
		//debug($ini);
		if ($ini['git-hg-rp']['rp-login'] && $ini['git-hg-rp']['rp-pw']) {
			$auth = $ini['git-hg-rp']['rp-login'].':'.$ini['git-hg-rp']['rp-pw'];

			$da = new DigestAuth('Rechnung+ API');
			$info = $da->POST('http://rechnung-plus.de/api/TipCat', $auth, $content);
			//debug($info);
			debug(json_decode($info['response'], true));
			echo 'POST time: '.$info['total_time'];
		} else {
			throw new Exception('rp-login/rp-pw not found in .hg/hgrc');
		}
	}

}
