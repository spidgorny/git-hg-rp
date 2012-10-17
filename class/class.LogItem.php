<?php

class LogItem {

	public $changeset;	// 3:5317600030a3
	public $rev;		// 3
	public $date;		// Wed Oct 17 11:58:36 2012 +0100

	function __construct($changeset, $date) {
		$this->changeset = $changeset;
		list($this->rev) = trimExplode(':', $this->changeset);
		$this->date = $date;
	}

	/**
	 *
	changeset:   3:5317600030a3
	tag:         tip
	user:        DEPIDSVY@hpg5k3j.local
	date:        Wed Oct 17 11:58:36 2012 +0100
	summary:     Fixed parsing the hg log

	 * @param array $collectedLog
	 * @return LogItem
	 */
	static function parseLines(array $collectedLog) {
		$assoc = array();
		foreach ($collectedLog as $line) {
			$pair = trimExplode(':', $line, 2);
			$assoc[$pair[0]] = $pair[1];
		}
		//debug('assoc', $assoc);
		$li = new LogItem($assoc['changeset'], $assoc['date']);
		return $li;
	}

}