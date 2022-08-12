# logrotate
PHP logrotate supports log level, file size limit, file number limit, and maximum storage days.

#usage
<?php
$logRt = LogRotate::getInstance();
$logRt->init(5, 2, './logs', 'mylog', 10);
$logRt->debug('This is a debug message');
$logRt->info('This is a info message');
$logRt->error('This is a error message');
