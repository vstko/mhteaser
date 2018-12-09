<?php


add_action('admin_menu', 'create_theme_options_page');

function create_theme_options_page() {
    add_options_page('Sequent Options',
        'Sequent Options',
        'administrator',
        __FILE__,
        'build_options_page');
}

function build_options_page() { ?>
    <div id="theme-options-wrap">
        <div class="icon32" id="icon-tools"> <br />
    </div>
    <h2>Sequent Options</h2>
    <p>Take control of your theme, by overriding the default settings with your own specific preferences.</p>
    <form method="post" action="options.php">
        <?php settings_fields('plugin_options'); ?>
        <?php do_settings_sections(__FILE__); ?>
        <p class="submit">
            <input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
        </p>
    </form>
    </div>
<?php }

add_action('admin_init', 'register_and_build_fields');

function register_and_build_fields() {
    register_setting('plugin_options', 'plugin_options', 'validate_setting');
    add_settings_section('main_section', 'Main Settings', 'section_cb', __FILE__);
    add_settings_field('x_api_key', 'Sequent x_api_key:', 'x_api_key_setting', __FILE__, 'main_section');
    add_settings_field('x_sequent_key', 'Sequent x_sequent_key:', 'x_sequent_key_setting', __FILE__, 'main_section');
    add_settings_field('sequen_api_base_url', 'Sequent Api Base Url:', 'sequen_api_base_url_setting', __FILE__, 'main_section');
}

function validate_setting($plugin_options) {
    return $plugin_options;
}

function section_cb() {}



function x_api_key_setting() {
    $options = get_option('plugin_options');
    echo "<input name='plugin_options[x_api_key]' type='text' value='{$options['x_api_key']}' />";
}

function x_sequent_key_setting() {
    $options = get_option('plugin_options');
    echo "<input name='plugin_options[x_sequent_key]' type='text' value='{$options['x_sequent_key']}' />";
}

function sequen_api_base_url_setting() {
    $options = get_option('plugin_options');
    echo "<input name='plugin_options[sequen_api_base_url]' type='text' value='{$options['sequen_api_base_url']}' />";
}