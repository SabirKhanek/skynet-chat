<?php
if (get_option("skynet_python_installation", "local") != "local") {
    echo "failed";
}

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $operatingSystem = "windows";
    add_option("skynet_host_os", "windows");
} else {
    $operatingSystem = "linux";
    add_option("skynet_host_os", "linux");
}

$plugin_dir = plugin_dir_path(__FILE__);

// Check operating system and run appropriate shell script
if ($operatingSystem == "windows") {
    $handle = popen('start /B ' . plugin_dir_path(__FILE__) . "/install_packages.bat > $plugin_dir/logs/python.logs 2>&1", 'r');
    $command = "start /B " . plugin_dir_path(__FILE__) . "/install_packages.bat > $plugin_dir/logs/python.logs 2>&1";
} else {
    $pathSH = plugin_dir_path(__FILE__) . "install_packages.sh";
    $handle = popen("bash `$pathSH` 2>&1 > python.logs", 'r');
}


if (!function_exists('isHostReachable')) {
    include plugin_dir_path(__FILE__) . "isHostReachable.php";
}

$host = get_option("local_python_server_host", "http://localhost:6929");

$start_time = time();

while (!isHostReachable($host)) {
    if (time() - $start_time > 120) {
        echo "failed";
        exit;
    }
    sleep(1);
}
echo "success";
