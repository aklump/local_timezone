<?php
/**
 * An example file to use for testing.
 */
require_once __DIR__ . '/src/LocalTimezone.php';
$timezone = (new \AKlump\LocalTimezone\LocalTimezone())->get();
var_dump($timezone);
