# Local Timezone

Provides a standard interface for getting the local, system timezone as a \DateTimeZone instance. This is not the same as relying on the PHP timezone setting, which may or may not be set. The determined value comes from the host system.

```php

date_create('tomorrow', \AKlump\LocalTimezone\LocalTimezone::get());
```

## Run the Example

To check compatibility you can run the example file from command line:

```shell
php -f example.php
```
