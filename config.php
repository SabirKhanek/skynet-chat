<?php
register_setting("skynet-chat-settings-group", "local_python_server_host");
register_setting("skynet-chat-settings-group", "skyet_python_installation");

add_settings_section("skynet-config-python", "Python Configuration", "skynet_config_python_cb", "skynet-chat-config");

add_settings_field("skynet-config-python-host", "Python Host", "skynet_config_python_host_cb", "skynet-chat-config", "skynet-config-python");
add_settings_field("skynet-config-python-installation", "Python Installation", "skynet_config_python_installation_cb", "skynet-chat-config", "skynet-config-python");
add_settings_field("skynet-config-python-host-reachable", "Python Host Reachable", "skynet_config_python_host_reachable_cb", "skynet-chat-config", "skynet-config-python");
add_settings_field("skynet-config-python-buttons", "Local Server Switch", "skynet_config_python_buttons_cb", "skynet-chat-config", "skynet-config-python");

// Enqueue the jQuery library
wp_enqueue_script("jquery");

// Add the JavaScript code to the page
add_action("admin_footer", "skynet_config_python_buttons_script");

function skynet_config_python_buttons_script()
{
?> <style>
        .loading {
            display: none;
        }

        .loading:before {
            content: "";
            box-sizing: border-box;
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin-top: -10px;
            margin-left: -10px;
            border-radius: 50%;
            border: 2px solid #ccc;
            border-top-color: #333;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

    <div class="loading"></div>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $("input[name='start_server']").click(function() {
                $.ajax({
                    url: "<?php echo admin_url("admin-ajax.php"); ?>",
                    type: "POST",
                    data: {
                        action: "start_server"
                    },
                    beforeSend: function() {
                        $(".loading").show();
                    },
                    success: function(response) {
                        if (response === "success") {
                            jQuery(".loading").hide();
                            $("input[name='start_server']").hide();
                            $("input[name='stop_server']").show();
                            alert("Server Started")
                        } else {
                            alert("Couldn't start the server. Please try again later.")
                        }
                        location.reload()
                    }
                });
            });
            $("input[name='stop_server']").click(function() {
                $.ajax({
                    url: "<?php echo admin_url("admin-ajax.php"); ?>",
                    type: "POST",
                    data: {
                        action: "stop_server"
                    },
                    beforeSend: function() {
                        $(".loading").show();
                    },
                    success: function(response) {
                        if (response === "success") {
                            jQuery(".loading").hide();
                            $("input[name='stop_server']").hide();
                            $("input[name='start_server']").show();
                            alert("Server stopped.")
                        } else {
                            alert("Couldn't stop the server. Please try again later.")
                        }
                        location.reload()
                    }
                });
            });
        });
    </script>
<?php
}

// Add the action for handling the AJAX requests
add_action("wp_ajax_start_server", "skynet_start_server_handler");
add_action("wp_ajax_stop_server", "skynet_stop_server_handler");

function skynet_start_server_handler()
{
    include plugin_dir_path(__FILE__) . "/start_local_server.php";
    wp_die();
}

function skynet_stop_server_handler()
{
    include plugin_dir_path(__FILE__) . "/stop_local_server.php";
    wp_die();
}

function skynet_config_python_cb()
{
    echo "<p>Python Configurations</p>";
}

function skynet_config_python_host_cb()
{
    $host = get_option("local_python_server_host", "http://locahost:6929");
    $installation = get_option("skynet_python_installation", "local");
    if ($installation == "remote") {
        echo "<input type='text' name='local_python_server_host' value='$host' />";
    } else {
        echo "<input type='text' name='local_python_server_host' value='$host' readonly/>";
    }
}

function skynet_config_python_installation_cb()
{
    $installation = get_option("skynet_python_installation", "local");
    // echo "<select name='skynet_python_installation'>";
    // echo "<option value='local' " . ($installation == "local" ? "selected" : "") . ">Local</option>";
    // echo "<option value='remote' " . ($installation == "remote" ? "selected" : "") . ">Remote</option>";
    // echo "</select>";
    echo "<input type='text' name='skynet_python_host_reachable' value='$installation' readonly />";
    update_option("skynet_python_installation", $installation);
}

function skynet_config_python_host_reachable_cb()
{
    $plugin_dir = plugin_dir_path(__FILE__);
    if (!function_exists("isHostReachable")) {
        include plugin_dir_path(__FILE__) . "isHostReachable.php";
    }
    $installation_type = get_option("skynet_python_installation", "local");
    $p_server = get_option("local_python_server_host", "http://localhost:6929");
    if ($installation_type == "remote" && $p_server == "unspecified" && !(str_starts_with($p_server, "http://") or str_starts_with($p_server, "https://"))) {
        $is_reachable = 0;
    } else {
        $is_reachable = isHostReachable(get_option("local_python_server_host", "http://localhost:6929"));
    }

    $reachable_text = $is_reachable ? 'Reachable' : 'Not Reachable';

    echo "<input type='text' name='skynet_python_host_reachable' value='$reachable_text' readonly />";
}

function skynet_config_python_buttons_cb()
{
    $installation = get_option("skynet_python_installation", "local");
    $host = get_option("local_python_server_host", "http://localhost:6929");


    if ($installation === "local") {
        if (!function_exists("isHostReachable")) {
            include plugin_dir_path(__FILE__) . "isHostReachable.php";
        }
        $is_reachable = isHostReachable($host);
        if (!$is_reachable) {
            echo "<input type='button' style='background-color: green;color: white;padding: 5px 10px;border-radius: 5px;border: none;cursor: pointer;' name='start_server' value='Start Server' />";
        } else {
            echo "<input type='button' style='background-color: red;color: white;padding: 5px 10px;border-radius: 5px;border: none;cursor: pointer;' name='stop_server' value='Stop Server' />";
        }
    }
}
