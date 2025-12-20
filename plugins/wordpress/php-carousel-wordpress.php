<?php
/**
 * Plugin Name: PHP Carousel
 * Plugin URI: https://github.com/julien-lin/php-carousel
 * Description: Intégration du PHP Carousel dans WordPress avec shortcode et widget
 * Version: 1.0.0
 * Author: Julien Linard
 * Author URI: https://github.com/julien-lin
 * License: MIT
 * Text Domain: php-carousel
 * Requires PHP: 8.2
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Check if PHP Carousel library is available
if (!class_exists('JulienLinard\Carousel\Carousel')) {
    // Try to autoload if composer is used
    if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
        require_once __DIR__ . '/../../vendor/autoload.php';
    } else {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p>';
            echo __('PHP Carousel library not found. Please install it via Composer.', 'php-carousel');
            echo '</p></div>';
        });
        return;
    }
}

use JulienLinard\Carousel\Carousel;

/**
 * PHP Carousel WordPress Integration
 */
class PHP_Carousel_WordPress
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Register shortcode
        add_shortcode('php_carousel', [$this, 'render_carousel']);
        
        // Register widget (if widgets are enabled)
        if (class_exists('WP_Widget')) {
            add_action('widgets_init', [$this, 'register_widget']);
        }
        
        // Add admin menu
        add_action('admin_menu', [$this, 'add_admin_menu']);
        
        // Enqueue admin scripts
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }

    /**
     * Render carousel shortcode
     * 
     * Usage: [php_carousel id="my-carousel" type="image" images="img1.jpg,img2.jpg" autoplay="true"]
     * 
     * @param array $atts Shortcode attributes
     * @return string Carousel HTML
     */
    public function render_carousel($atts): string
    {
        // Parse attributes
        $atts = shortcode_atts([
            'id' => 'carousel-' . uniqid(),
            'type' => 'image',
            'images' => '',
            'items' => '',
            'autoplay' => 'true',
            'autoplay_interval' => '5000',
            'loop' => 'true',
            'show_arrows' => 'true',
            'show_dots' => 'true',
            'items_per_slide' => '1',
            'transition' => 'slide',
            'theme' => 'auto',
        ], $atts, 'php_carousel');

        try {
            // Build options
            $options = [
                'autoplay' => filter_var($atts['autoplay'], FILTER_VALIDATE_BOOLEAN),
                'autoplayInterval' => (int) $atts['autoplay_interval'],
                'loop' => filter_var($atts['loop'], FILTER_VALIDATE_BOOLEAN),
                'showArrows' => filter_var($atts['show_arrows'], FILTER_VALIDATE_BOOLEAN),
                'showDots' => filter_var($atts['show_dots'], FILTER_VALIDATE_BOOLEAN),
                'itemsPerSlide' => (int) $atts['items_per_slide'],
                'transition' => $atts['transition'],
                'theme' => $atts['theme'],
            ];

            // Get items
            $items = [];
            if (!empty($atts['images'])) {
                // Comma-separated images
                $images = array_map('trim', explode(',', $atts['images']));
                $items = array_map(function($img) {
                    return ['image' => esc_url($img)];
                }, $images);
            } elseif (!empty($atts['items'])) {
                // JSON items
                $items = json_decode($atts['items'], true) ?: [];
            }

            if (empty($items)) {
                return '<p>' . __('No items provided for carousel.', 'php-carousel') . '</p>';
            }

            // Create carousel based on type
            $type = sanitize_text_field($atts['type']);
            $id = sanitize_text_field($atts['id']);

            $carousel = match($type) {
                'image' => Carousel::image($id, $items, $options),
                'card' => Carousel::card($id, $items, $options),
                'testimonial' => Carousel::testimonial($id, $items, $options),
                'gallery' => Carousel::gallery($id, $items, $options),
                'infinite' => Carousel::infiniteCarousel($id, $items, $options),
                default => Carousel::image($id, $items, $options),
            };

            return $carousel->render();
        } catch (\Exception $e) {
            if (current_user_can('manage_options')) {
                return '<p class="php-carousel-error">' . esc_html($e->getMessage()) . '</p>';
            }
            return '';
        }
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu(): void
    {
        add_options_page(
            __('PHP Carousel Settings', 'php-carousel'),
            __('PHP Carousel', 'php-carousel'),
            'manage_options',
            'php-carousel',
            [$this, 'admin_page']
        );
    }

    /**
     * Admin page
     */
    public function admin_page(): void
    {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <div class="card">
                <h2><?php _e('Shortcode Usage', 'php-carousel'); ?></h2>
                <p><?php _e('Use the following shortcode to display a carousel:', 'php-carousel'); ?></p>
                <code>[php_carousel id="my-carousel" type="image" images="image1.jpg,image2.jpg"]</code>
                
                <h3><?php _e('Available Types', 'php-carousel'); ?></h3>
                <ul>
                    <li><code>image</code> - Image carousel</li>
                    <li><code>card</code> - Card carousel</li>
                    <li><code>testimonial</code> - Testimonial carousel</li>
                    <li><code>gallery</code> - Gallery with thumbnails</li>
                    <li><code>infinite</code> - Infinite scrolling</li>
                </ul>
                
                <h3><?php _e('Available Options', 'php-carousel'); ?></h3>
                <ul>
                    <li><code>autoplay</code> - true/false (default: true)</li>
                    <li><code>autoplay_interval</code> - milliseconds (default: 5000)</li>
                    <li><code>loop</code> - true/false (default: true)</li>
                    <li><code>show_arrows</code> - true/false (default: true)</li>
                    <li><code>show_dots</code> - true/false (default: true)</li>
                    <li><code>items_per_slide</code> - number (default: 1)</li>
                    <li><code>transition</code> - slide/fade/cube (default: slide)</li>
                    <li><code>theme</code> - auto/light/dark (default: auto)</li>
                </ul>
            </div>
        </div>
        <?php
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook): void
    {
        if ($hook !== 'settings_page_php-carousel') {
            return;
        }
        // Add admin styles if needed
    }

    /**
     * Register widget
     */
    public function register_widget(): void
    {
        register_widget('PHP_Carousel_Widget');
    }
}

/**
 * PHP Carousel Widget
 */
class PHP_Carousel_Widget extends \WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'php_carousel_widget',
            __('PHP Carousel', 'php-carousel'),
            ['description' => __('Display a carousel', 'php-carousel')]
        );
    }

    public function widget($args, $instance): void
    {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        $shortcode = '[php_carousel';
        $shortcode .= ' id="' . esc_attr($instance['id'] ?? 'widget-' . $this->id) . '"';
        $shortcode .= ' type="' . esc_attr($instance['type'] ?? 'image') . '"';
        
        if (!empty($instance['images'])) {
            $shortcode .= ' images="' . esc_attr($instance['images']) . '"';
        }
        
        foreach (['autoplay', 'loop', 'show_arrows', 'show_dots'] as $option) {
            if (isset($instance[$option])) {
                $shortcode .= ' ' . $option . '="' . esc_attr($instance[$option]) . '"';
            }
        }
        
        $shortcode .= ']';
        
        echo do_shortcode($shortcode);
        echo $args['after_widget'];
    }

    public function form($instance): void
    {
        $title = $instance['title'] ?? '';
        $id = $instance['id'] ?? 'widget-' . $this->id;
        $type = $instance['type'] ?? 'image';
        $images = $instance['images'] ?? '';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'php-carousel'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                   type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('id')); ?>"><?php _e('Carousel ID:', 'php-carousel'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('id')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('id')); ?>" 
                   type="text" value="<?php echo esc_attr($id); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('type')); ?>"><?php _e('Type:', 'php-carousel'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('type')); ?>" 
                    name="<?php echo esc_attr($this->get_field_name('type')); ?>">
                <option value="image" <?php selected($type, 'image'); ?>><?php _e('Image', 'php-carousel'); ?></option>
                <option value="card" <?php selected($type, 'card'); ?>><?php _e('Card', 'php-carousel'); ?></option>
                <option value="testimonial" <?php selected($type, 'testimonial'); ?>><?php _e('Testimonial', 'php-carousel'); ?></option>
                <option value="gallery" <?php selected($type, 'gallery'); ?>><?php _e('Gallery', 'php-carousel'); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('images')); ?>"><?php _e('Images (comma-separated):', 'php-carousel'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('images')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('images')); ?>" 
                   type="text" value="<?php echo esc_attr($images); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance): array
    {
        $instance = [];
        $instance['title'] = sanitize_text_field($new_instance['title'] ?? '');
        $instance['id'] = sanitize_text_field($new_instance['id'] ?? '');
        $instance['type'] = sanitize_text_field($new_instance['type'] ?? 'image');
        $instance['images'] = sanitize_text_field($new_instance['images'] ?? '');
        return $instance;
    }
}

// Initialize plugin
new PHP_Carousel_WordPress();

