<?php
/**
 * Timber starter-theme
 * https://github.com/timber/starter-theme
 *
 * @package    WordPress
 * @subpackage Timber
 * @since      Timber 0.1
 */

 include __DIR__ . '/acf_fields.php';

/**
 * If you are installing Timber as a Composer dependency in your theme, you'll need this block
 * to load your dependencies and initialize Timber. If you are using Timber via the WordPress.org
 * plug-in, you can safely delete this block.
 */
$composer_autoload = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $composer_autoload ) ) {
	include_once $composer_autoload;
	Timber\Timber::init();
}

/**
 * This ensures that Timber is loaded and available as a PHP class.
 * If not, it gives an error message to help direct developers on where to activate
 */
if ( ! class_exists( 'Timber' ) ) {

	add_action(
		'admin_notices',
		function () {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		}
	);

	add_filter(
		'template_include',
		function ( $template ) {
			return get_template_directory() . '/static/no-timber.html';
		}
	);
	return;
}

/**
 * Sets the directories (inside your theme) to find .twig files
 */
$static_timber_dirs = array(
	'src/twig/layouts',
	'src/twig/pages',
	'src/twig/components',
);

$theme_base = get_stylesheet_directory() . '/';

$component_dirs = array_map(
	function ( $str ) use ( &$theme_base ) {
		return str_replace( $theme_base, '', $str );
	},
	array_filter( glob( $theme_base . 'src/twig/components/*' ), 'is_dir' )
);

Timber::$dirname = array_merge( $static_timber_dirs, $component_dirs );

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;

/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
class ArtsSite extends Timber\Site {

	/**
	 * Add timber support.
	 */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'acf/init', array( $this, 'my_acf_init' ) );
		parent::__construct();
	}

	/**
	 * This is where you can register custom post types.
	 */
	public function register_post_types() {
		// REGISTER POST TYPE HERE
	}

	public function my_acf_init() {

		// check function exists
		if ( function_exists( 'acf_register_block' ) ) {

			// REGISTER BLOCKS HERE
		}
	}

	public function theme_supports() {
		if ( function_exists( 'acf_add_options_page' ) ) {
			acf_add_options_page(
				array(
					'page_title' => 'Global Settings',
					'menu_title' => 'Global Settings',
					'menu_slug'  => 'global-settings',
					'redirect'   => false,
				)
			);
		}

	}
}

new ArtsSite();

/**
 * Enqueue scripts and styles.
 */
function arts_theme_scripts() {
	// remove parent
	wp_dequeue_style( 'hvh' );
	wp_dequeue_script( 'main' );

	// child
	wp_enqueue_style( 'child_css', get_stylesheet_directory_uri() . '/dist/styles/scripts.css', array(), date( 'H:i:s' ) );
	wp_enqueue_script( 'child_scripts', get_stylesheet_directory_uri() . '/dist/scripts.js', array(), date( 'H:i:s' ), true );
}
add_action( 'wp_enqueue_scripts', 'arts_theme_scripts', 100 );

add_filter( 'auto_core_update_send_email', '__return_false' );

// remove shortlinks in <head>
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );


// Function 1: Gets the post IDs based on page category slug
function get_post_ids_to_convert($post_type, $term_slug) {
    global $wpdb;

    // Ensure post type and term slug are provided
    if (empty($post_type) || empty($term_slug)) {
        return [];
    }

    // Set taxonomy based on post type and log
    if ($post_type === 'page') {
        $taxonomy = 'page-categories';
    } elseif ($post_type === 'post') {
        $taxonomy = 'category';
    } else {
        WP_CLI::error("Invalid post type '$post_type'. Supported types are 'page' and 'post'.");
    }

    // Log details
    WP_CLI::log("Post Type: $post_type");
    WP_CLI::log("Taxonomy: $taxonomy");
    WP_CLI::log("Term Slug: $term_slug");

    // SQL query to fetch post IDs
    $sql = "
        SELECT p.ID
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
        INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
        WHERE p.post_type = %s
        AND p.post_status = %s
        AND tt.taxonomy = %s
        AND t.slug = %s
    ";

    $post_status = 'draft';

    // Prepare the query with variables
    $query = $wpdb->prepare($sql, $post_type, $post_status, $taxonomy, $term_slug);

    // Run the query to get post IDs
    $post_ids = $wpdb->get_col($query);

    // Check if posts were found
    if (!empty($post_ids)) {
        WP_CLI::log("Number of posts found: " . count($post_ids));
    } else {
        WP_CLI::log("No posts found for the given parameters.");
    }

    return $post_ids; // Return the array of IDs
}


// Function 2: Converts HTML in pages to Colby blocks
function convert_content_to_colby_blocks($post_type, $term_slug) {
    global $wpdb;

    // Get post IDs from the first function
    $post_ids = get_post_ids_to_convert($post_type, $term_slug);

    // Ensure there are post IDs to process
    if (empty($post_ids)) {
        echo 'No posts found.';
        return;
    }

    $upload_dir = wp_get_upload_dir();
    $base_upload_url = $upload_dir['baseurl'];

    // Iterate through the post IDs
    foreach ($post_ids as $post_id) {
        // Get the raw post content for the current post ID
        $post_content = get_post_field('post_content', $post_id);

        // Step 1: Remove any wrapping <p> and </p> tags surrounding the entire post content
        $post_content = preg_replace('/<p(.*?)>/i', '', $post_content);
        $post_content = preg_replace('/<\/p>/i', '', $post_content);

        // Step 2: Convert all headings (H1 through H6) to the custom heading block
        $post_content = preg_replace_callback(
            '/<h([1-6])(.*?)>(.*?)<\/h\1>/s',
            function ($matches) {
                $heading_level = $matches[1]; // Extract the heading level (e.g., 1, 2, etc.)
                $heading_text = preg_replace('/<br\s*\/?>/i', '', $matches[3]); // Remove <br> tags
                $heading_text = strip_tags(trim($heading_text)); // Strip all other tags and trim whitespace

                return "<!-- wp:heading {\"level\":$heading_level} -->\n<h$heading_level class=\"wp-block-heading\">$heading_text</h$heading_level>\n<!-- /wp:heading -->";
            },
            $post_content
        );

        // Step 3: Convert all <img> tags to the custom ACF image block
        $post_content = preg_replace_callback(
            '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i',
            function ($matches) use ($base_upload_url, $wpdb) {
                $img_url = $matches[1]; // Extract the image URL

                // Parse the image URL to get the relative path
                $parsed_url = parse_url($img_url);
                $relative_path = isset($parsed_url['path']) ? $parsed_url['path'] : '';

                // Remove size suffixes from filenames (e.g., -229x300)
                $relative_path = preg_replace('/^(.+)-\d+x\d+(\.[a-zA-Z]+)$/', '$1$2', $relative_path);

                // Reconstruct the image URL for the current environment
                if (strpos($relative_path, '/wp-content/uploads/') === 0) {
                    $converted_url = $base_upload_url . str_replace('/wp-content/uploads', '', $relative_path);
                } else {
                    $converted_url = $base_upload_url . $relative_path;
                }

                // Extract just the filename (e.g., Example-Image-2.jpg)
                $filename = basename($converted_url);

                // Remove file extensions for fuzzy matching (e.g., Example-Image-2)
                $base_filename = preg_replace('/\.[a-zA-Z]+$/', '', $filename);

                // Query the database for a matching GUID (fuzzy match)
                $query = $wpdb->prepare("
                    SELECT ID, guid
                    FROM {$wpdb->posts}
                    WHERE post_type = 'attachment'
                    AND guid LIKE %s
                    LIMIT 1
                ", '%' . $wpdb->esc_like($base_filename) . '%');

                $attachment = $wpdb->get_row($query);

                // Get the image ID
                $image_id = $attachment ? $attachment->ID : null;

                // If no image ID is found, return the original <img> tag
                if (!$image_id) {
                    return $matches[0];
                }

                // Build the custom ACF image block
                $acf_image_block = '<!-- wp:acf/image {"name":"acf/image","data":{"image":' . $image_id . ',"_image":"field_637648002429f","media_caption":"0","_media_caption":"field_638783ea00b86","align_center":"","_align_center":"field_6377adbc99a0b","image_scale":"100","_image_scale":"field_637ba00835500","image_path":"","_image_path":"field_637bc1cb68bf0"},"mode":"edit"} /-->';

                // Replace the <img> tag with the ACF block
                return $acf_image_block;
            },
            $post_content
        );

				// Step 4: Wrap remaining non-block content in paragraph blocks
        $post_content = wrap_paragraph_after_headings($post_content);
        $post_content = wrap_paragraph_after_acf_blocks($post_content);
				$post_content = wrap_paragraph_before_blocks($post_content);

        // Update the post content with the converted content
        wp_update_post(array(
            'ID' => $post_id,
            'post_content' => $post_content,
        ));

        WP_CLI::log("Post ID: $post_id - Content converted and updated.");
    }
}

// Function 3
function wrap_paragraph_after_headings($content) {
    $pattern = '/(<!--\s*\/wp:[^ ]+\s*-->)(\s*(?:(?!<!--\s*wp:).)+)/s';

    $content = preg_replace_callback($pattern, function ($matches) {
        $trimmed_text = trim($matches[2]);

        // Convert special characters to Unicode
        $unicode_text = preg_replace_callback('/[\x00-\x1F\x22\x5C]/u', function ($char) {
            return sprintf('\u%04x', ord($char[0]));
        }, $trimmed_text);

				$unicode_text = str_replace("\u0009", "", $unicode_text);
				$unicode_text = str_replace("\u000a", "", $unicode_text);
				$unicode_text = str_replace("\u0022", "", $unicode_text);

        return $matches[1] . '<!-- wp:acf/paragraph {"name":"acf/paragraph","data":{"paragraph_text":"' . $unicode_text . '","_paragraph_text":"field_637634c55da0b"},"mode":"edit"} /-->';
    }, $content);

    return $content;
}

// Function 4
function wrap_paragraph_after_acf_blocks($content) {
    $pattern = '/(<!--\s*wp:acf[^>]*\/-->)(\s*(?:(?!<!--\s*wp:).)+)/s';

    $content = preg_replace_callback($pattern, function ($matches) {
        $trimmed_text = trim($matches[2]);

        // Convert special characters to Unicode
        $unicode_text = preg_replace_callback('/[\x00-\x1F\x22\x5C]/u', function ($char) {
            return sprintf('\u%04x', ord($char[0]));
        }, $trimmed_text);

				$unicode_text = str_replace("\u0009", "", $unicode_text);
				$unicode_text = str_replace("\u000a", "", $unicode_text);
				$unicode_text = str_replace("\u0022", "", $unicode_text);

        return $matches[1] . '<!-- wp:acf/paragraph {"name":"acf/paragraph","data":{"paragraph_text":"' . $unicode_text . '","_paragraph_text":"field_637634c55da0b"},"mode":"edit"} /-->';
    }, $content);

    return $content;
}

// Function 5
function wrap_paragraph_before_blocks($content) {
    $pattern = '/^(?!<!--\s*wp:)(.*?)(?=<!--\s*wp:)/s';

    $content = preg_replace_callback($pattern, function ($matches) {
        $trimmed_text = trim($matches[1]);

        // Convert special characters to Unicode
        $unicode_text = preg_replace_callback('/[\x00-\x1F\x22\x5C]/u', function ($char) {
            return sprintf('\u%04x', ord($char[0]));
        }, $trimmed_text);

				$unicode_text = str_replace("\u0009", "", $unicode_text);
				$unicode_text = str_replace("\u000a", "", $unicode_text);
				$unicode_text = str_replace("\u0022", "", $unicode_text);

        return '<!-- wp:acf/paragraph {"name":"acf/paragraph","data":{"paragraph_text":"' . $unicode_text . '","_paragraph_text":"field_637634c55da0b"},"mode":"edit"} /-->';
    }, $content);

    return $content;
}


// CLI command: wp convert_content --type=page --term=page-category-slug
// CLI command: wp convert_content --type=post --term=post-category-slug
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('convert_content', function ($args, $assoc_args) {
        $post_type = $assoc_args['type'] ?? null;
        $term_slug = $assoc_args['term'] ?? null;

        if (empty($post_type)) {
            WP_CLI::error('Error: You must provide a post type using --type=<post_type>.');
        }
        if (empty($term_slug)) {
            WP_CLI::error('Error: You must provide a term slug using --term=<slug>.');
        }

        WP_CLI::log("Starting conversion for $post_type(s) with term slug: $term_slug...");

        convert_content_to_colby_blocks($post_type, $term_slug);

        WP_CLI::success("Content conversion for $post_type(s) completed successfully.");
    });
}
