--TEST--
Debug\ErrorHook\Catcher: notices and warnings
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

echo $non_existed;
fopen("non-existed", "r");

?>

--EXPECT--
Notification: array (
  'errno' => 'E_NOTICE',
  'errstr' => 'Undefined variable: non_existed',
  'errfile' => '020_notice.php',
  'errline' => '*',
  'tracecount' => 0,
)

Notice: Undefined variable: non_existed in * on line *
Notification: array (
  'errno' => 'E_WARNING',
  'errstr' => 'fopen(non-existed): failed to open stream: No such file or directory',
  'errfile' => '020_notice.php',
  'errline' => '*',
  'tracecount' => 1,
)

Warning: fopen(non-existed): failed to open stream: No such file or directory in * on line *