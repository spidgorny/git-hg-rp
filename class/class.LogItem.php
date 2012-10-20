<?php

class LogItem {

	public $changeset;	// 3:5317600030a3
	public $rev;		// 3
	public $user;
	public $date;		// Wed Oct 17 11:58:36 2012 +0100
	public $files = array();
	public $description;

	function __construct($changeset, $user, $date, array $files, $description) {
		$this->changeset = $changeset;
		list($this->rev) = explode(':', $this->changeset);
		$this->user = $user;
		$this->date = $date;
		$this->files = $files;
		$this->description = $description;
		//debug(get_object_vars($this));
	}

	/**
	 *
	$ hg log -v
	changeset:   4:2ad94db57397
	tag:         tip
	user:        Slawa HP <spidgorny@gmail.com>
	date:        Sat Oct 20 01:59:36 2012 +0100
	files:       class/class.VCS_RechnungPlus.php
	description:
	multiline
	checkpoint


	changeset:   3:b84bd6c393e2
	user:        Slawa HP <spidgorny@gmail.com>
	date:        Sat Oct 20 01:58:44 2012 +0100
	files:       class/class.DigestAuth.php class/class.VCS_RechnungPlus.php
	description:
	checkpoint

	 * @param array $collectedLog
	 * @return LogItem
	 */
	static function parseLines(array $collectedLog) {
		$assoc = array();
		$description = NULL;
		foreach ($collectedLog as $line) {
			if (is_array($description)) {
				$description[] = $line;
			} else {
				$pair = trimExplode(':', $line, 2);
				$assoc[$pair[0]] = $pair[1];
				if ($pair[0] == 'description') {
					$description = array();
				}
			}
		}
		if ($assoc) {
			$assoc['description'] = implode("\n", $description);
			//debug('$collectedLog', $collectedLog);
			//debug('assoc', $assoc);
			//debug('desc', $description);
			$li = new LogItem(
				$assoc['changeset'],
				$assoc['user'],
				$assoc['date'],
				explode(' ', $assoc['files']),
				$assoc['description']
			);
			return $li;
		}
	}

}
