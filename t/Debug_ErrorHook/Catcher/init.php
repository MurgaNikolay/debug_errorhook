<?php
require_once dirname(__FILE__) . "/../init.php";

$printListener = new Debug\ErrorHook\Listener();
$printListener->addNotifier(new PrintNotifier());
