<?php
namespace Debug\ErrorHook;
/**
 * Notifier interface.
 * Should be implemented by all notifiers in the stack.
 */
interface INotifier
{
	/**
	 * Called when an error occurred.
	 * 
	 * @param string $errNo
	 * @param string $errStr
	 * @param string $errFile
	 * @param string $errLine
	 * @param array $trace
	 * @return void
	 */
    public function notify($errNo, $errStr, $errFile, $errLine, $trace);
}
