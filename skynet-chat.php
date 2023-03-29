<?php
/*
Plugin Name: Skynet-Chat
Description: Plugin to enable voice assistant chatbot
Version: 1.0
Author: devsabi
Plugin URI: #
*/
if (!defined("ABSPATH")) {
    header("Location: /");
    die("ACCESS DENIED!");
}


function python_installer_activate()
{
    include "skynet_chat_activate.php";
}

register_activation_hook(__FILE__, "python_installer_activate");

function skynet_chat_deactivate()
{
    include "skynet_chat_deactivate.php";
}

register_deactivation_hook(__FILE__, "skynet_chat_deactivate");


function skynet_chat_box()
{
    include "skynet_chat_box.php";
}



add_shortcode("skynet_chat_box", "skynet_chat_box");

function skynet_chat_scripts()
{
    $host = get_option("local_python_server_host", "http://localhost:6929/");
    wp_add_inline_script('skynet-audio-js', 'const host = "' . $host . '";', 'before');
    // wp_enqueue_script("skynet-recorder-js", plugins_url("js/recorder.js", __FILE__), array('jquery'), filemtime(plugin_dir_path(__FILE__) . "js/recorder.js"), true);
    // wp_enqueue_script("skynet-audio-js", plugins_url("js/audio.js", __FILE__), array(), filemtime(plugin_dir_path(__FILE__) . "js/audio.js"), true);
}

wp_enqueue_style('skynet_chat_css', plugins_url("assets/css/style.css", __FILE__), array(), filemtime(plugin_dir_path(__FILE__) . "assets/css/style.css"));

add_action("wp_enqueue_scripts", "skynet_chat_scripts");


function skynet_chat_config_page()
{
    if (!current_user_can("manage_options")) {
        wp_die("You do not have sufficient permissions to access this page.");
    }
?>
    <div class="wrap">
        <form action="options.php" method="post">
            <?php
            settings_fields("skynet-chat-settings-group");
            do_settings_sections("skynet-chat-config");
            submit_button('Save Changes');
            ?>
        </form>
    </div>
<?php
}

function skynet_chat_config_page_2()
{
    if (!current_user_can("manage_options")) {
        wp_die("You do not have sufficient permissions to access this page.");
    }
    include plugin_dir_path(__FILE__) . "/keygen_page.php";
}

function skynet_chat_config_menu()
{
    add_options_page("Skynet Config", "Skynet Configuration", "manage_options", "skynet-chat-config", "skynet_chat_config_page", 6);
    add_options_page("Skynet Config", "Skynet license keygen", "manage_options", "skynet-chat-config_2", "skynet_chat_config_page_2", 7);
}

add_action("admin_menu", "skynet_chat_config_menu");

function skynet_chat_register_settings()
{
    include plugin_dir_path(__FILE__) . "/config.php";
}

add_action("admin_init", "skynet_chat_register_settings");


/*
ABSPATH is defined when the action comes from wordpress
WP_UNINSTALL_PLUGIN is defined when the action comes from the uninstall.php file
array_change_key_case: change the case of all key pairs
shortcode_atts: provide default attributes for the shortcode arguments
wp_add_inline_script("skynet-recorder-js", "console.log('" . filemtime(plugin_dir_path(__FILE__) . "js/recorder.js") . "')");
function skynet_chat_activate()
{
    global $wpdb, $table_prefix;
    $table_name = $table_prefix . "skynet_dummy";
    $q = "CREATE TABLE IF NOT EXISTS`$table_name` (`col_1` VARCHAR(50) NOT NULL , `col_2` VARCHAR(50) NOT NULL , `col_3` INT NOT NULL ) ENGINE = InnoDB;";
    $wpdb->query($q);
}
C:\Python310\Scripts\
C:\Python310\
*/

?>