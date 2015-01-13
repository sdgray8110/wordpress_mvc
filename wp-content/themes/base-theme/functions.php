<?php
add_action( 'after_setup_theme', 'theme_setup' );
add_action( 'admin_init', 'admin_actions' );
add_action( 'admin_menu', 'admin_setup' );
add_filters();

function theme_setup() {
    add_actions();
    bootstrap_classes();
    new GlobalSettings();
    shortcodes();
    add_editor_style('editor-style.css');
    add_static_assets();
    set_thumbnail_sizes();
    hide_admin_bar();
}

function admin_setup() {

}

function add_static_assets() {
    if (is_admin()) {
        admin_js();
        admin_css();
    } else {
        public_js();
    }
}

function public_js() {
    wp_deregister_script('jquery');
    wp_register_script('jquery', get_stylesheet_directory_uri() . '/js/lib/jquery.1.8.3.min.js');
    wp_register_script('mustache', get_stylesheet_directory_uri() . '/js/mustache.js');
    wp_register_script('serializeObject', get_stylesheet_directory_uri() . '/js/serializeObject.js');
    wp_register_script('fancybox2', get_stylesheet_directory_uri() . '/js/lib/jquery.fancybox.pack.js');
    wp_register_script('validation', get_stylesheet_directory_uri() . '/js/lib/validation.js');
    wp_register_script('global', get_stylesheet_directory_uri() . '/js/global.js');

    wp_enqueue_script('jquery');
    wp_enqueue_script('mustache');
    wp_enqueue_script('serializeObject');
    wp_enqueue_script('fancybox2');
    wp_enqueue_script('global');
    wp_enqueue_script('validation');
}

function admin_js() {
    wp_register_script('mustache', get_stylesheet_directory_uri() . '/js/mustache.js');
    wp_register_script('serializeObject', get_stylesheet_directory_uri() . '/js/serializeObject.js');
    wp_register_script('svAdmin', get_stylesheet_directory_uri() . '/js/admin.js');

    wp_enqueue_script('mustache');
    wp_enqueue_script('serializeObject');
    wp_enqueue_script('svAdmin');
}

function admin_css() {
    wp_register_style('admin', get_stylesheet_directory_uri() . '/admin.css');

    wp_enqueue_style('admin');
}

function add_filters() {
    add_filter('image_send_to_editor','give_linked_images_class',10,8);
}

function add_actions() {

}

function admin_actions() {

}

function members_only_meta_box() {

}

function hide_admin_bar() {
    if (!current_user_can('publish_posts')) {
        show_admin_bar(false);
    }
}

function shortcodes() {

}

function set_thumbnail_sizes() {
    if ( function_exists( 'add_theme_support' ) ) {
        add_theme_support( 'post-thumbnails' );
        set_post_thumbnail_size( 250, 250 ); // default Post Thumbnail dimensions
    }

    if ( function_exists( 'add_image_size' ) ) {

    }
}

function bootstrap_classes() {
    $dir = get_stylesheet_directory();

    foreach (glob($dir . '/classes/*.php') as $filename) {
        if (!excluded_class($filename)) {
            require_once($filename);
        }
    }

    foreach (glob(get_stylesheet_directory() . '/models/*.php') as $filename) {
        require_once($filename);
    }

    foreach (glob(get_stylesheet_directory() . '/models/base/*.php') as $filename) {
        require_once($filename);
    }

    foreach (glob(get_stylesheet_directory() . '/models/modules/*.php') as $filename) {
        require_once($filename);
    }

    foreach (glob(get_stylesheet_directory() . '/models/postTypes/*.php') as $filename) {
        require_once($filename);
    }
}

function excluded_class($path) {
    $excluded = array();
    $arr = explode('/', $path);
    $filename = $arr[count($arr) - 1];

    return in_array($filename, $excluded);
}

function remove_legacy_roles() {
    remove_role('subscriber');
    remove_role('editor');
    remove_role('author');
    remove_role('contributor');
}

function nav_menus() {
    if ( function_exists( 'register_nav_menus' ) ) {
        register_nav_menus(
            array(
                'header_menu' => 'Main Menu',
                'footer_menu' => 'Footer Menu'
            )
        );
    }
}

function get_page_by_slug($slug) {
    $args = array(
        'name' => $slug,
        'post_type' => 'page',
        'post_status' => 'publish',
        'showposts' => 1
    );

    $posts = get_posts($args);

    return $posts[0];
}

function get_permalink_by_slug($slug) {
    $page = get_page_by_slug($slug);

    return get_permalink($page->ID);
}

function is_homepage() {
    $home = get_page_by_slug('home');
    $id = get_the_ID();

    return $home->ID == $id;
}

function time_ago_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    if ($diff->d >= 60) {
        return $ago->format('M j, Y');
    }

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

function shorten($string, $length) {
    // By default, an ellipsis will be appended to the end of the text.
    $suffix = '...';

    // Convert 'smart' punctuation to 'dumb' punctuation, strip the HTML tags,
    // and convert all tabs and line-break characters to single spaces.
    $short_desc = trim(str_replace(array("\r","\n", "\t"), ' ', strip_tags($string)));

    // Cut the string to the requested length, and strip any extraneous spaces
    // from the beginning and end.
    $desc = trim(substr($short_desc, 0, $length));

    // Find out what the last displayed character is in the shortened string
    $lastchar = substr($desc, -1, 1);

    // If the last character is a period, an exclamation point, or a question
    // mark, clear out the appended text.
    if ($lastchar == '.' || $lastchar == '!' || $lastchar == '?') $suffix='';

    // Append the text.
    $desc .= $suffix;

    // Send the new description back to the page.
    return $desc;
}

function archive_page_slug() {
    $post_type = get_post_type();

    if ($post_type) {
        $post_type_data = get_post_type_object( $post_type );
        return $post_type_data->rewrite['slug'];
    }
}

/**
 * Attach a class to linked images' parent anchors
 * e.g. a img => a.img img
 */
function give_linked_images_class($html, $id, $caption, $title, $align, $url, $size, $alt = '' ){
    $classes = 'modal'; // separated by spaces, e.g. 'img image-link'

    // check if there are already classes assigned to the anchor
    if ( preg_match('/<a.*? class=".*?">/', $html) ) {
        $html = preg_replace('/(<a.*? class=".*?)(".*?>)/', '$1 ' . $classes . '$2', $html);
    } else {
        $html = preg_replace('/(<a.*?)>/', '$1 class="' . $classes . '" >', $html);
    }
    return $html;
}

function makeClickableLinks($s) {
    return preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank">$1</a>', $s);
}