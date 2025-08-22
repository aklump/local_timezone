<?php

namespace AKlump\LocalTimezone;

/**
 * Get the local timezone per the system.
 *
 * You should cache the results of this for best performance, as each additional
 * call incurs the same overhead.
 *
 * @url https://www.baeldung.com/linux/current-system-time-zone
 * @url https://learn.microsoft.com/en-us/openspecs/sharepoint_protocols/ms-wssfo/3eac0888-b5f9-4c78-9077-a421fc0513f8
 * @url https://github.com/unicode-org/cldr/blob/main/common/supplemental/windowsZones.xml
 */
class LocalTimezone {

  /**
   * Get the local timezone as a DateTimeZone object.
   *
   * This method attempts to determine the system's timezone using several
   * methods, prioritizing environment variables and falling back to OS-specific
   * commands.  It handles both Windows and non-Windows systems and throws an
   * exception if the timezone cannot be determined.
   *
   * @return \DateTimeZone The local timezone as a DateTimeZone object.
   * @throws \RuntimeException If the system timezone cannot be determined.
   */
  public static function get(): \DateTimeZone {
    if (self::isWindows()) {
      // On Windows, use tzutil to get the timezone ID.
      $windows_timezone_id = trim(exec('tzutil /g', $output, $return_code));;
      if ($return_code !== 0 || empty($windows_timezone_id)) { // Check for errors
        throw new \RuntimeException(sprintf("Error getting Windows Timezone. tzutil returned: %s", implode("\n", $output)));
      }
      // Convert the Windows timezone ID to an IANA timezone name.
      $system_timezone_name = static::fromWindows($windows_timezone_id);
    }
    else {
      // On non-Windows systems, use the `date` command.
      $system_timezone_name = exec('date +%Z');
      // Convert the abbreviation to a full timezone name using PHP's function.
      $system_timezone_name = timezone_name_from_abbr($system_timezone_name);
    }

    if (!$system_timezone_name) {
      throw new \RuntimeException('Unable to determine system timezone');
    }

    return timezone_open($system_timezone_name);
  }

  /**
   * Check if the system is Windows.
   *
   * @return bool True if the system is Windows, false otherwise.
   */
  private static function isWindows(): bool {
    return (defined('PHP_OS_FAMILY') && PHP_OS_FAMILY === 'Windows')
      || strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
  }

  /**
   * Convert a Windows timezone ID to an IANA timezone name.
   *
   * This function uses a statically cached lookup table loaded from
   * win_to_iana.php.  The table maps Windows timezone IDs to IANA timezone
   * names as defined in the Unicode CLDR.
   *
   * @param string $win_timezone_id The Windows timezone ID.
   *
   * @return string The IANA timezone name, or an empty string if not found.
   *
   * @see https://github.com/unicode-org/cldr/blob/main/common/supplemental/windowsZones.xml
   */
  private static function fromWindows(string $win_timezone_id): string {
    static $win_to_iana;
    if (!isset($win_to_iana)) {
      $win_to_iana = require __DIR__ . '/data/win_to_iana.php';
    }

    return $win_to_iana[$win_timezone_id] ?? '';
  }
}
