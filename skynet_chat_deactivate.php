<?php
delete_option("local_python_server_host");;
delete_option("skynet_python_installation");
delete_option("skynet_host_os");

$plugin_dir = plugin_dir_path(__FILE__);

$pid = file_get_contents($plugin_dir . "skynet-py/server.pid");
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $operatingSystem = "windows";
} else {
    $operatingSystem = "linux";
}
if ($operatingSystem == "windows") {
    $command = "taskkill /F /PID $pid";
} else {
    $command = "kill -9 $pid";
}
$output = shell_exec($command);
file_put_contents($plugin_dir . "/logs/actions.log", "Server with PID $pid stopped with output $output.\n", FILE_APPEND);
