<?php
/*
Plugin Name: Team Slider
Plugin URI: 
Description: Creates a team member post type and displays them in a sliding carousel
Version: 1.0
Author: Kahlil Calavas
Author URI: 
License: GPL2
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Register Custom Post Type
function register_team_member_post_type() {
    $labels = array(
        'name'                  => _x('Team Members', 'Post Type General Name', 'text-domain'),
        'singular_name'         => _x('Team Member', 'Post Type Singular Name', 'text-domain'),
        'menu_name'            => __('Team Members', 'text-domain'),
        'name_admin_bar'       => __('Team Member', 'text-domain'),
        'archives'             => __('Team Member Archives', 'text-domain'),
        'attributes'           => __('Team Member Attributes', 'text-domain'),
        'all_items'            => __('All Team Members', 'text-domain'),
        'add_new_item'         => __('Add New Team Member', 'text-domain'),
        'add_new'             => __('Add New', 'text-domain'),
        'new_item'            => __('New Team Member', 'text-domain'),
        'edit_item'           => __('Edit Team Member', 'text-domain'),
        'update_item'         => __('Update Team Member', 'text-domain'),
        'view_item'           => __('View Team Member', 'text-domain'),
        'view_items'          => __('View Team Members', 'text-domain'),
        'search_items'        => __('Search Team Member', 'text-domain'),
    );
    
    $args = array(
        'label'               => __('Team Member', 'text-domain'),
        'labels'              => $labels,
        'supports'            => array('title', 'thumbnail'),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-groups',
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => false,
        'exclude_from_search' => true,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
    );
    
    register_post_type('team_member', $args);
}
add_action('init', 'register_team_member_post_type');

// Add thumbnail support if theme doesn't have it
function team_slider_theme_support() {
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'team_slider_theme_support');

// Enqueue styles
function team_slider_enqueue_styles() {
    wp_enqueue_style(
        'team-slider-style',
        plugins_url('css/team-slider.css', __FILE__),
        array(),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'team_slider_enqueue_styles');

// Create plugin CSS file on activation
function team_slider_activate() {
    // Create CSS directory if it doesn't exist
    $css_dir = plugin_dir_path(__FILE__) . 'css';
    if (!file_exists($css_dir)) {
        mkdir($css_dir, 0755, true);
    }
    
    // Create CSS file with styles
    $css_content = '
.team-slider {
    display: flex;
    width: 100%;
    overflow: hidden;
    padding: 20px 0;
    background: #f5f5f5;
}

.team-container {
    display: flex;
    animation: slide 40s linear infinite;
}

.team-container:hover {
    animation-play-state: paused;
}

.team-member {
    flex: 0 0 250px;
    margin: 0 20px;
    text-align: center;
}

.team-member img {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    object-fit: cover;
    filter: grayscale(100%);
    transition: filter 0.3s ease;
}

.team-member:hover img {
    filter: grayscale(0%);
}

.team-member h3 {
    margin: 10px 0 5px;
    color: #333;
}

@keyframes slide {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-50%);
    }
}';
    
    file_put_contents($css_dir . '/team-slider.css', $css_content);
}
register_activation_hook(__FILE__, 'team_slider_activate');

// Shortcode function
function team_slider_shortcode() {
    // Query team members
    $team_members = get_posts(array(
        'post_type' => 'team_member',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC'
    ));
    
    if (empty($team_members)) {
        return '<p>No team members found.</p>';
    }
    
    // Start output buffering
    ob_start();
    ?>
    <div class="team-slider">
        <div class="team-container">
            <?php
            // First set
            foreach ($team_members as $member) {
                $image = get_the_post_thumbnail_url($member->ID, 'full');
                if (!$image) {
                    $image = plugins_url('images/default-avatar.png', __FILE__);
                }
                ?>
                <div class="team-member">
                    <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($member->post_title); ?>">
                    <h3><?php echo esc_html($member->post_title); ?></h3>
                </div>
                <?php
            }
            
            // Duplicate set for seamless loop
            foreach ($team_members as $member) {
                $image = get_the_post_thumbnail_url($member->ID, 'full');
                if (!$image) {
                    $image = plugins_url('images/default-avatar.png', __FILE__);
                }
                ?>
                <div class="team-member">
                    <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($member->post_title); ?>">
                    <h3><?php echo esc_html($member->post_title); ?></h3>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    <?php
    
    return ob_get_clean();
}
add_shortcode('team_slider', 'team_slider_shortcode');

// Clean up on uninstall
function team_slider_uninstall() {
    // Remove CSS directory and files
    $css_dir = plugin_dir_path(__FILE__) . 'css';
    if (file_exists($css_dir . '/team-slider.css')) {
        unlink($css_dir . '/team-slider.css');
    }
    if (is_dir($css_dir)) {
        rmdir($css_dir);
    }
}
register_uninstall_hook(__FILE__, 'team_slider_uninstall');
