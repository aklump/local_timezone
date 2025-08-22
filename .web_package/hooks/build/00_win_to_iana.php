<?php
/**
 * Download the current windows timezone ID to IANA mapping as store as PHP array.
 */
$url = 'https://raw.githubusercontent.com/unicode-org/cldr/refs/heads/main/common/supplemental/windowsZones.xml';
$data = simplexml_load_file($url);
$win_to_iana = [];
foreach ($data->windowsZones->mapTimezones->mapZone as $zone) {
  $win_key = (string) $zone->attributes()->other;
  $win_to_iana[$win_key] = (string) $zone->attributes()->type;
}
ksort($win_to_iana);
$output = __DIR__ . '/../../../src/data/win_to_iana.php';
if (!is_dir(dirname($output))) {
  mkdir(dirname($output), 0755, TRUE);
}
file_put_contents($output, "<?php\n\n/* THIS FILE IS AUTO-GENERATED; DO NOT EDIT !*/\n\nreturn " . var_export($win_to_iana, TRUE) . ';');
