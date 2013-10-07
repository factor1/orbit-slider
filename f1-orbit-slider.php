<?php
/*
 Plugin Name: F1 Orbit Slider
 Description: Slide show manager
 Version: 1.1
 Author: Factor1
 Author URI: http://www.factor1studios.com/
*/
$f1OrbitSliderPlugin = new f1OrbitSliderPlugin();

class f1OrbitSliderPlugin
{
    public $capability = 'manage_options';

    public $db_table_slides;
    public $db_table_slider;

    public $wordpress_upload = false;
    public $plugin_dir = false;
    public $slide_photo_dir = 'orbit-slides-photos/';
    public $slide_photo_thumb_dir = 'orbit-slides-photos/thumbs/';

    public $allowed_image_types = array('jpeg', 'jpg', 'png', 'gif');

    public $image_thumb_size = array(450, 300);

    public $slide_fields = array(
        'id' => false,
        'status' => 1,
        'display_order' => 999,
        'image' => '',
        'caption' => '',
        'url' => '',
        'slider_id' => false,
    );

    public $slider_fields = array(
        'id' => false,
        'title' => 'default',
        'slug' => false,
        'animation' => 'slide',
        'timer_speed' => 10000,
        'pause_on_hover' => true,
        'resume_on_mouseout' => true,
        'animation_speed' => 500,
        'stack_on_small' => true,
        'navigation_arrows' => true,
        'slide_number' => true,
        'bullets' => true,
        'timer' => true,
    );

    public $_department_choices = null;

    function __construct(){
        global $wpdb;
        $this->wordpress_upload = wp_upload_dir();
        $this->plugin_dir = plugin_dir_path(__FILE__);

        $this->db_table_slides = '`'.$wpdb->base_prefix.'f1_orbit_slide`';
        $this->db_table_slider = '`'.$wpdb->base_prefix.'f1_orbit_slider`';

        #Admin Menu
        add_action( 'admin_menu', array( &$this, 'add_top_level_menu' ) );
        #Load CSS/Javascripts
        add_action( 'admin_init', array(&$this, 'admin_enqueue_scripts') );
        #Load ajax calls
        if(is_admin() ) {
            #add_action('wp_ajax_f1_gn_mentor_admin_details', array(&$this, 'display_admin_details'));
        } else {
            wp_register_style( 'foundation', plugins_url('css/foundation.min.css', __FILE__) );
            wp_enqueue_style( 'foundation' );

            wp_enqueue_script( 'foundation', plugins_url( '/js/foundation.min.js', __FILE__ ), array('jquery'));
            wp_enqueue_script( 'foundation-orbit', plugins_url( '/js/foundation/foundation.orbit.js', __FILE__ ), array('foundation'));
        }
        #load our shortcodes
        add_shortcode( 'f1_orbit_slider', array(&$this, 'shortcode_slider') );

        register_activation_hook( __FILE__, array(&$this, 'install'));

    }
    public function install() {
        if(!is_dir($this->wordpress_upload['basedir']."/".$this->slide_photo_dir)) {
            mkdir($this->wordpress_upload['basedir']."/".$this->slide_photo_dir);
            mkdir($this->wordpress_upload['basedir']."/".$this->slide_photo_thumb_dir);
        }
        global $wpdb;


        $wpdb->query("CREATE TABLE IF NOT EXISTS ".$this->db_table_slider." (
               `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(150) NOT NULL,
                `slug` varchar(150) NOT NULL,
                `animation` int(11) NOT NULL,
                `timer_speed` int(11) NOT NULL,
                `pause_on_hover` tinyint(1) NOT NULL,
                `resume_on_mouseout` tinyint(1) NOT NULL,
                `animation_speed` int(11) NOT NULL,
                `stack_on_small` tinyint(1) NOT NULL,
                `navigation_arrows` tinyint(1) NOT NULL,
                `slide_number` tinyint(1) NOT NULL,
                `bullets` tinyint(1) NOT NULL,
                `timer` tinyint(1) NOT NULL,
                PRIMARY KEY (`id`),
                KEY `title` (`title`,`slug`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

        $wpdb->query("CREATE TABLE IF NOT EXISTS ".$this->db_table_slides." (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `status` int(11) NOT NULL,
                `display_order` int(11) NOT NULL,
                `image` varchar(255) NOT NULL,
                `caption` varchar(255) DEFAULT NULL,
                `url` varchar(255) DEFAULT NULL,
                `slider_id` int(11) NOT NULL,
                PRIMARY KEY (`id`),
                KEY `status` (`status`,`display_order`,`slider_id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

    }
    function create_blank_slide() {
        return (object) $this->slide_fields;
    }
    function create_blank_slider() {
        return (object) $this->slider_fields;
    }
    function admin_enqueue_scripts($hook) {

        wp_register_style( 'f1OrbitSliderStyle', plugins_url('style.css', __FILE__) );
        wp_enqueue_style( 'f1OrbitSliderStyle' );

        wp_enqueue_script( 'f1OrbitSliderGlobalJs', plugins_url( '/js/global.js', __FILE__ ), array('jquery', 'jquery-ui-sortable'));
        // wp_enqueue_script( 'f1OrbitSliderFormJs', plugins_url( '/js/jquery.form.js', __FILE__ ), array('jquery'));
        // in javascript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
        wp_localize_script( 'f1OrbitSliderGlobalJs', 'f1OrbitSlider_object',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),

            ) );
    }
    function add_top_level_menu()
    {

        // Settings for the function call below
        $page_title = 'F1 Slider';
        $menu_title = 'Orbit Slider';
        $menu_slug = 'f1-orbit-slider';
        $function = array( &$this, 'display_admin_index' );
        #$icon_url = NULL;
        #$position = '';

        // Creates a top level admin menu - this kicks off the 'display_page()' function to build the page
        #$page = add_menu_page($page_title, $menu_title, $this->capability, $menu_slug, $function, $icon_url, 10);

        add_menu_page($page_title, $menu_title, $this->capability, $menu_slug,$function);

        add_submenu_page (null, 'Orbit Slider', '', $this->capability, 'f1-orbit-slides', array(&$this, 'display_admin_details'));
    }
    public function display_admin_index() {
        global $wpdb;
        $sliders = $wpdb->get_results("SELECT * FROM ".$this->db_table_slider." ORDER BY `title` ASC");
        echo $this->render('admin_index.tpl.php', array('sliders' => $sliders));
    }
    public function display_admin_details() {
        global $wpdb;
        if(empty($_REQUEST['id'])) {
            $slider = $this->create_blank_slider();
        } else {
            $slider = $this->load_slider($_REQUEST['id']);
        }
        if('POST' == $_SERVER['REQUEST_METHOD']) {
            ini_set('display_errors', 'on');
            error_reporting(-1);
            $slider_data = $_POST['slider'];
            $fields = "";
            foreach($this->slider_fields as $field=>$default_value) {

                if(!in_array($field, array('id')) && isset($slider_data[$field])) {

                    if(is_numeric($slider_data[$field])) {
                        $fields .= "`".$field."` = ".mysql_real_escape_string($slider_data[$field]).", ";
                    } else {
                        $fields .= "`".$field."` = '".mysql_real_escape_string($slider_data[$field])."', ";
                    }
                    $slider->$field = $slider_data[$field];
                }
            }
            $fields = rtrim($fields, ", ");
            if($slider->id) {
                $wpdb->query("UPDATE ".$this->db_table_slider." SET ".$fields." WHERE `id` = ".$slider->id);

            } else {
                $wpdb->query("INSERT INTO ".$this->db_table_slider." SET ".$fields."");
                $slider = $wpdb->get_row("SELECT * FROM ".$this->db_table_slider." ORDER BY `id` DESC LIMIT 1");
            }
            #work the slides now
            for($i = 0; $i < count($_POST['slide_ids']); $i++) {

                if(empty($_POST['slide_ids'][$i])) {
                    $slide = $this->create_blank_slide();
                } else {
                    $slide = $this->load_slide($_POST['slide_ids'][$i]);
                }
                $slide->caption = $_POST['caption'][$i];
                $slide->url = $_POST['url'][$i];
                $slide->status = $_POST['status'][$i];
                $slide->slider_id = $slider->id;
                if(is_uploaded_file($_FILES['image']['tmp_name'][$i])) {
                    #echo 'have an uploaded file: '.$_FILES['image']['tmp_name'][$i]."<br />";
                    $file_name = $_FILES['image']['name'][$i];
                    $ext = explode('.', $file_name);
                    $ext = $ext[count($ext)-1];
                    $file_name_stripped = str_replace('.'.$ext, '', $file_name);
                    while(is_file($this->wordpress_upload['basedir']."/".$this->slide_photo_dir.$file_name_stripped.".".$ext)) {
                        $file_name_stripped = str_replace('.'.$ext, '', $file_name)."-".rand(500, 5000);
                    }
                    if(move_uploaded_file($_FILES['image']['tmp_name'][$i], $this->wordpress_upload['basedir']."/".$this->slide_photo_dir.$file_name_stripped.".".$ext)) {
                        #echo 'have uploaded  '.$file_name_stripped.".".$ext."<br />";
                        $slide->image = $file_name_stripped.".".$ext;

                        $image = wp_get_image_editor( $this->wordpress_upload['basedir']."/".$this->slide_photo_dir.$slide->image );
                        if ( ! is_wp_error( $image ) ) {
                            #resize large image
                            #resize thumb
                            $image->resize( 240, 160, true );
                            $image->save( $this->wordpress_upload['basedir']."/".$this->slide_photo_thumb_dir.$slide->image );
                        }
                    }
                } else {
                    #echo 'no upload file <br />';
                }
                if($slide->image) {
                    if($slide->id) {
                        if(isset($_POST['delete_slide']) && in_array($slide->id, $_POST['delete_slide'])) {
                            $wpdb->query("DELETE FROM ".$this->db_table_slides."
                            WHERE `id` = ".mysql_real_escape_string($slide->id));
                            @unlink($this->wordpress_upload['basedir']."/".$this->slide_photo_dir.$slide->image);
                            @unlink($this->wordpress_upload['basedir']."/".$this->slide_photo_thumb_dir.$slide->image);
                        } else {
                            $wpdb->query("UPDATE ".$this->db_table_slides." SET
                            `caption` = '".mysql_real_escape_string($slide->caption)."',
                            `url` = '".mysql_real_escape_string($slide->url)."',
                            `status` = ".mysql_real_escape_string($slide->status).",
                            `display_order` = ".$i.",
                            `image` = '".mysql_real_escape_string($slide->image)."'
                            WHERE `id` = ".mysql_real_escape_string($slide->id));
                        }

                    } else {
                        $wpdb->query("INSERT INTO ".$this->db_table_slides." SET
                            `caption` = '".mysql_real_escape_string($slide->caption)."',
                            `url` = '".mysql_real_escape_string($slide->url)."',
                            `status` = ".mysql_real_escape_string($slide->status).",
                            `display_order` = ".$i.",
                            `slider_id` = ".mysql_real_escape_string($slide->slider_id).",
                            `image` = '".mysql_real_escape_string($slide->image)."'");
                    }
                }
            }
            #exit();
            wp_redirect(admin_url('admin.php?page=f1-orbit-slider'));
            exit();
        }
        $slides = $this->load_slides_from_slider($slider);
        $blank_slide = $this->create_blank_slide();
        echo $this->render('admin_details.tpl.php',array('slider' => $slider, 'slides' => $slides, 'blank_slide' => $blank_slide));
        exit();
    }
    public function load_slides_from_slider($slider) {
        global $wpdb;
        if($slider->id) {
            return $wpdb->get_results("SELECT * FROM ".$this->db_table_slides." WHERE `slider_id` = ".mysql_real_escape_string($slider->id)." AND `status` = 1 ORDER BY `display_order`");
        }
        return array();
    }
    public function shortcode_slider($args) {
        global $wpdb;
        if(isset($args['slug'])) {
           $slider = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$this->db_table_slider." WHERE `slug` LIKE %s", $args['slug']));
        } else {
            $slider = $wpdb->get_row("SELECT * FROM ".$this->db_table_slider." LIMIT 1");
        }
        if(!$slider) {
            return 'No Slider Selected';
        }
        $slides = $this->load_slides_from_slider($slider);
        wp_register_style( 'f1SliderPublicStyle', plugins_url('style_public.css', __FILE__) );
        wp_enqueue_style( 'f1SliderPublicStyle' );
        return $this->render("slider_display.tpl.php", array('slides' => $slides, 'slider' => $slider));
    }
    function load_slider($id) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$this->db_table_slider." WHERE `id` = %s", $id));
    }
    function load_slide($id) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$this->db_table_slides." WHERE `id` = %s", $id));
    }
    function slider_animation_options() {
        return array(
            1 => 'slide',
            2 => 'fade',
        );
    }
    function get_slider_animate_text($slider) {
        $animations = $this->slider_animation_options();
        if(isset($animations[$slider->animation])) {
            return $animations[$slider->animation];
        }
        return 'fade';
    }
    function render($__tpl__, $vars = array()) {
        if(is_array($vars)) {
            foreach($vars as $__ky__=>$__val__) {
                $$__ky__ = $__val__;
            }
        }

        ob_start();
        include($this->plugin_dir."templates/".$__tpl__);
        $_output = ob_get_contents();
        ob_end_clean();
        return $_output;
    }
    public function slugify($term) {
        $term = preg_replace('/[^a-z0-9\-_\.]/', '_', strtolower($term));
        $term = preg_replace('/_+/', '_', $term);
        return $term;
    }
    public function display_slide_thumb_url($slide) {
        if($slide->image && is_file($this->wordpress_upload['basedir']."/".$this->slide_photo_thumb_dir.$slide->image)) {
            return $this->wordpress_upload['baseurl']."/".$this->slide_photo_thumb_dir.$slide->image;
        }
        return '';
    }
    public function display_slide_photo_url($slide) {
        if($slide->image && is_file($this->wordpress_upload['basedir']."/".$this->slide_photo_dir.$slide->image)) {
            return $this->wordpress_upload['baseurl']."/".$this->slide_photo_dir.$slide->image;
        }
        return '';
    }
    public function delete_slide_photos($slide) {
        @unlink($this->wordpress_upload['basedir']."/".$this->slide_photo_thumb_dir.$slide->image);
        @unlink($this->wordpress_upload['basedir']."/".$this->slide_photo_dir.$slide->image);
        $slide->image = '';
    }
}