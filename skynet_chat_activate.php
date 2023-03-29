<?php

$pythonCommand = "python -V";
$python3Command = "python3 -V";

$pythonOutput = shell_exec($pythonCommand);
$python3Output = shell_exec($python3Command);

$logFile = plugin_dir_path(__FILE__) . '/logs/python-installer-activation.log';

if ($pythonOutput) {
    file_put_contents($logFile, "Python is installed on the server and its version is: $pythonOutput\n", FILE_APPEND);
    if (!get_option('skynet_python_installation')) {
        add_option('skynet_python_installation', 'local');
    }
    if (!get_option('local_python_server_host')) {
        add_option('local_python_server_host', 'http://localhost:6929');
    }
    $python = "python";
} else {
    if (!get_option('skynet_python_installation')) {
        $opt = add_option('skynet_python_installation', 'remote');
        file_put_contents($logFile, "$opt ", FILE_APPEND);
    }
    if (!get_option('local_python_server_host')) {
        $opt = add_option('local_python_server_host', 'https://skynetserver.pythonanywhere.com');
        file_put_contents($logFile, "$opt ", FILE_APPEND);
    }
    file_put_contents($logFile, "Python is not installed on the server.\n", FILE_APPEND);
    return;
}

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $operatingSystem = "windows";
    if (!get_option("skynet_host_os")) {
        add_option("skynet_host_os", "windows");
    }
} else {
    $operatingSystem = "linux";
    if (!get_option("skynet_host_os")) {
        add_option("skynet_host_os", "linux");
    }
}

$plugin_dir = plugin_dir_path(__FILE__);

// Check operating system and run appropriate shell script
if ($operatingSystem == "windows") {
    $handle = popen('start /B ' . plugin_dir_path(__FILE__) . "/install_packages.bat > $plugin_dir/logs/python.logs 2>&1", 'r');
    $command = "start /B " . plugin_dir_path(__FILE__) . "/install_packages.bat > $plugin_dir/logs/python.logs 2>&1";
} else {
    $dirPath = plugin_dir_path(__FILE__);
    $pathSH = plugin_dir_path(__FILE__) . "/install_packages.sh";
    $handle = popen("bash `$pathSH` 2>&1 > $dirPath/logs/python.logs", 'r');
}



$host = get_option("local_python_server_host", "http://localhost:6929");

// file_put_contents($logFile, $host, FILE_APPEND);

if (!function_exists('isHostReachable')) {
    include plugin_dir_path(__FILE__) . "isHostReachable.php";
}


$start_time = time();

while (!isHostReachable($host)) {
    // file_put_contents($logFile, $host . time() - $start_time, FILE_APPEND);

    if (time() - $start_time > 5) {
        update_option("skynet_python_installation", "remote");
        update_option("local_python_server_host", "https://devsabi-skynet.herokuapp.com/");

        return;
    }
    sleep(1);
}
