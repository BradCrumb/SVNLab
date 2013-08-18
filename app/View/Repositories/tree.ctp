<?php
if (!empty($files)) {
	echo $this->element('tree', array('files' => $files, 'latestLog' => $latestLog, 'parentTree' => $parentTree));
}