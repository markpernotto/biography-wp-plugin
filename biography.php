<?php
/*
Plugin Name: Custom Ty Biography
Description: Plugin that extends functionality to display a small pic and biography of Ty above selected blog posts
Version: 0.0.0.3
Author: mark.pernotto
Author URI: https://pernotto.com/
License: GPLv2 or later
*/

if ( !function_exists( 'add_action' ) ) {
  echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
  exit;
}

class Tys_Great_Bio
{
    private $options;
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }
    public function add_plugin_page()
    {
        add_options_page(
            'Ty Biography', 
            'Biography', 
            'manage_options', 
            'biography', 
            array( $this, 'create_admin_page' )
        );
    }
    public function create_admin_page()
    {
        $this->options = get_option( 'biography_options' );
        ?>
        <div class="wrap">
            <form method="post" action="options.php">
            <?php
                settings_fields( 'biography' );
                do_settings_sections( 'biography' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }
    public function page_init()
    {        
        register_setting(
            'biography',
            'biography_options'
        );
        add_settings_section(
            'biography_section', 
            __( 'Ty\'s Great Biography.', 'wporg' ),
            array( $this, 'print_section_info' ), 
            'biography' 
        );  
        add_settings_field(
            'biography_public', 
            'Bio Public Display', 
            array( $this, 'bio_public_callback' ), 
            'biography', 
            'biography_section'
        ); 
        add_settings_field(
            'biography_photo', 
            'Bio Photograph', 
            array( $this, 'bio_photo_callback' ), 
            'biography', 
            'biography_section' 
        );      
        add_settings_field(
            'biography_desc', 
            'Bio Description', 
            array( $this, 'bio_desc_callback' ), 
            'biography', 
            'biography_section'
        ); 
        add_settings_field(
            'biography_mobile', 
            'Bio Mobile', 
            array( $this, 'bio_mobile_callback' ), 
            'biography', 
            'biography_section'
        ); 
    }
    public function print_section_info()
    {
        print 'Please find the tools to be able to upload/update your biography to your blog post pages.';
    }
    public function bio_photo_callback()
    {
        printf(
            '<input type="text" id="bio_photo" name="biography_options[bio_photo]" value="%s" style="display:none;"/>',
            isset( $this->options['bio_photo'] ) ? esc_attr( $this->options['bio_photo']) : ''
        );
        printf(
            '<button class="button wpse-228085-upload">Upload</button>'
        );
        
        isset( $this->options['bio_photo'] ) ?>
            <img id="img_bio_photo" src="<?php echo $this->options["bio_photo"];?>" style="width:150px;" />
        <?php   
    }
    public function bio_desc_callback()
    {
        wp_editor( $this->options['bio_desc'], 'bio_desc', array(
            'wpautop'       => true,
            'media_buttons' => false,
            'textarea_name' => 'biography_options[bio_desc]',
            'textarea_rows' => 10
        ) );
    }
    public function bio_mobile_callback()
    {
        printf(
            'Check this box to not display on mobile: <input type="checkbox" id="bio_mobile" name="biography_options[bio_mobile]" %s />',
            isset( $this->options['bio_mobile'] ) ? 'checked' : ''
        );
    }
    public function bio_public_callback()
    {
        printf(
            'Check this box to make the plugin live: <input type="checkbox" id="bio_public" name="biography_options[bio_public]" %s />',
            isset( $this->options['bio_public'] ) ? 'checked' : ''
        );
    }
}
add_action('admin_enqueue_scripts', function(){   
    if( empty( $_GET['page'] ) || "biography" !== $_GET['page'] ) { return; }

    wp_enqueue_media();
});
add_action('admin_footer', function() { 
    if( empty( $_GET['page'] ) || "biography" !== $_GET['page'] ) { return; }
?>
    <script>
        jQuery(document).ready(function($){
            var custom_uploader;
            var click_elem = jQuery('.wpse-228085-upload');
            var target = jQuery('#bio_photo');
            var img_target = jQuery('#img_bio_photo');
            click_elem.click(function(e) {
                e.preventDefault();
                if (custom_uploader) {
                    custom_uploader.open();
                    return;
                }
                custom_uploader = wp.media.frames.file_frame = wp.media({
                    title: 'Choose Image',
                    button: {
                        text: 'Choose Image'
                    },
                    multiple: false
                });
                custom_uploader.on('select', function() {
                    attachment = custom_uploader.state().get('selection').first().toJSON();
                    target.val(attachment.url);
                    img_target.attr('src',attachment.url);
                });
                custom_uploader.open();
            });      
        });
    </script>
    <?php
});
if( is_admin() )
    $my_settings_page = new Tys_Great_Bio();

function get_Tys_Bio(){
    $get_option = get_option('biography_options');
    if($get_option['bio_public']){
        if($get_option['bio_mobile']){
            $dont_show_bio = 'dont_show_bio"';
        }
        echo "<div class='$dont_show_bio' id='post-content' >";
        echo '<div style="width:100%">';
        echo '<div style="width:16%;padding:2%;float:left;">'; ?>
        <img src="<?php echo $get_option['bio_photo'];?>" />
        <?php
        echo '</div>';
        echo '<div class="content" style="width:76%;padding:2%;float:left;font-style:italic">';
        echo '<p>'.$get_option['bio_desc'].'</p>';
        echo '</div>';
        echo "</div>";
    }
}
?>
