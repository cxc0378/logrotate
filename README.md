# logrotate
PHP logrotate is a simple method to record logs by rotation, which supports log level, file size limit, file number limit, and maximum saved days.

PHP 5.4 and later.

Log rotation:
```
mylog.log -> mylog_1.log
mylog_1.log -> mylog_2.log
mylog_2.log -> mylog_3.log
```

Usage:
```php
<?php
$logRt = LogRotate::getInstance();
$logRt->init(5, 2, './logs', 'mylog', 10);
$logRt->debug('This is a debug message');
$logRt->info('This is a info message');
$logRt->error('This is a error message');
```
