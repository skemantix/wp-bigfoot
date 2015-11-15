<?php
class WP_Bigfoot_Settings
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        
        add_options_page(
                    'Settings Admin', 
                    'Footnotes', 
                    'manage_options', 
                    'wp-bigfoot-admin', 
                    array( $this, 'create_admin_page' )
                );

    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'my_option_name' );
        ?>
        <div class="wrap">
            <h2>WP Bigfoot</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'wp-bigfoot-admin' );   
                do_settings_sections( 'wp-bigfoot-admin' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'wp-bigfoot_option_group', // Option group
            'my_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Bigfoot.js options', // Title
            array( $this, 'print_section_info' ), // Callback
            'wp-bigfoot-admin' // Page
        );  

        add_settings_field(
            'actionOriginalFN', // ID
            __('actionOriginalFN','wpbigfoot'), // Title 
            array( $this, 'actionOriginalFN_number_callback' ), // Callback
            'wp-bigfoot-admin', // Page
            'wp-bigfoot_pluginPage_section' // Section           
        );      

        add_settings_field(
            'activateOnHover', 
            __('activateOnHover', 'wpbigfoot'), 
            array( $this, 'activateOnHover_callback' ), 
            'wp-bigfoot-admin', 
            'wp-bigfoot_pluginPage_section'
        );      
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['id_number'] ) )
            $new_input['id_number'] = absint( $input['id_number'] );

        if( isset( $input['title'] ) )
            $new_input['title'] = sanitize_text_field( $input['title'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Create simple, elegant footnotes on your site. Use the `[footnote]` shortcode and the plugin takes care of the rest.<br>Example usage: `Lorem ipsum. [footnote]My note.[/footnote]`<br><br>See <a href="http://www.bigfootjs.com/#options" target="_blank">the documentation</a> for details.<br><br><hr><br>';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function actionOriginalFN_number_callback()
    {
        printf(
            //'<input type="text" id="id_number" name="my_option_name[id_number]" value="%s" />',
						'<input type="radio" id="actionOriginalFN" name="actionOriginalFN[]" value="%s" />',
            isset( $this->options['actionOriginalFN'] ) ? esc_attr( $this->options['actionOriginalFN']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function activateOnHover_callback()
    {
        printf(
            '<input type="text" id="title" name="my_option_name[title]" value="%s" />',
            isset( $this->options['title'] ) ? esc_attr( $this->options['title']) : ''
        );
    }
}

if( is_admin() )
    $wp_bigfoot_settings = new WP_Bigfoot_Settings();