<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.sprucetech.com/
 * @since             1.0.0
 * @package           Spruce_api_extension
 *
 * @wordpress-plugin
 * Plugin Name:       Spruce API Extension
 * Plugin URI:        https://www.sprucetech.com/
 * Description:       A Spruce extension that offers a suite of features, including a Youtube live stream feed, Youtube channel feed, and an interactive JavaScript Map. Developed specifically for Senator Cardin's website. 
 * Version:           3.0.12
 * Author:            Jun Huang
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       spruce_api_extension
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SPRUCE_API_EXTENSION_VERSION', '3.0.12' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-spruce_api_extension-activator.php
 */
function activate_spruce_api_extension() {
require_once plugin_dir_path( __FILE__ ) . 'includes/class-spruce_api_extension-activator.php';
Spruce_api_extension_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-spruce_api_extension-deactivator.php
 */
function deactivate_spruce_api_extension() {
require_once plugin_dir_path( __FILE__ ) . 'includes/class-spruce_api_extension-deactivator.php';
Spruce_api_extension_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_spruce_api_extension' );
register_deactivation_hook( __FILE__, 'deactivate_spruce_api_extension' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-spruce_api_extension.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_spruce_api_extension() {

$plugin = new Spruce_api_extension();
$plugin->run();

}
run_spruce_api_extension();

function spruce_extension_get_streams () {
$YT_video_url = "";
$YT_video_id = "";
$YT_video_query = array(
'post_type' => 'live_video',
'post_status' => 'private',

);
$YT_video_post = new WP_Query($YT_video_query);

if($YT_video_post->have_posts()) {
while($YT_video_post->have_posts()) {
	$YT_video_post->the_post();

// 			echo var_dump($post_id);
// 			echo var_dump(get_field('video_url'));
	if (!get_field('video_url')) {
		return "no live videos";
	} else {
		$YT_video_url = get_field('video_url');
		preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $YT_video_url, $match);
		$YT_video_id = $match[1];
	}
}
}

$video_title =  explode('</title>', explode('<title>', file_get_contents($YT_video_url))[1])[0];
// echo var_dump($video_title);
ob_start();
?>

<div class="">
	<div class="et_pb_column et_pb_column_1_2 et_pb_column_2  et_pb_css_mix_blend_mode_passthrough">
		<div class="et_pb_module et_pb_text et_pb_text_1  et_pb_text_align_left et_pb_bg_layout_light">
			<div class="et_pb_text_inner">
				<h1 style="text-align: left;">Watch Live</h1>
			</div>
		</div>
		<div class="et_pb_module et_pb_code et_pb_code_2">
			<div class="et_pb_code_inner">
				<p style="color: #B11F29; margin-bottom: 1rem;">
					<?php echo esc_html(date("F j, Y")); ?>
				</p>

				<h3 style="color: black; font-weight: bold; line-height: 2rem;">
					<?php echo esc_html($video_title); ?>
				</h3>
			</div>
		</div>
		<div class="et_pb_button_module_wrapper et_pb_button_1_wrapper et_pb_button_alignment_center et_pb_module ">
			<a class="et_pb_button et_pb_button_1 link-flash et_pb_bg_layout_light"
				href="<?php echo esc_url( $YT_video_url ); ?>" target="_blank">Watch
				Live</a>
		</div>
	</div>
	<div class="et_pb_column et_pb_column_1_2 et_pb_column_3  et_pb_css_mix_blend_mode_passthrough et-last-child">
		<iframe style="height: 100%; width: auto; aspect-ratio: 16 / 9;"
			src="https://www.youtube.com/embed/<?php echo $YT_video_id; ?>" allowfullscreen> </iframe>
	</div>
</div>

<?php 
return ob_get_clean();   
}

function spruce_extension_get_cert_letters($year) {
$cert_letters_arr = [];
// new query for cert letter url based on subcommittee
$cert_letter_query = array(
'post_type' => 'certification_letter',
'post_status' => 'publish',
'meta_query' => array(
	array(
	'key'	 	=> 'year',
	'value'	  	=> $year,
	'compare' 	=> '=',
	)
)
);
$cert_letter_post = new WP_Query($cert_letter_query);

if($cert_letter_post->have_posts()) {
while($cert_letter_post->have_posts()) {
	$cert_letter_post->the_post();
	$cert_letter_url = get_field('certification_letter');
	$subcommittee = get_field('subcommittee');
	$cert_letters_arr[$subcommittee] = $cert_letter_url;
}
}

return $cert_letters_arr;			
}

function pagination_bar($wp_query) {
$total_pages = $wp_query->max_num_pages;

if ($total_pages > 1){
$current_page = max(1, get_query_var('paged')); 
echo paginate_links(array(
	'base' => get_pagenum_link(1) . '%_%',
	'format' => '/page/%#%',
	'current' => $current_page,
	'total' => $total_pages,
));
}
}

function spruce_extension_earmarks($atts) {
	$is_funded_page = false;
// get year and category attribute values
extract( shortcode_atts( array(
'category' => '',
'year' => ''
), $atts ) );

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$args = array(
'posts_per_page' => -1,
// 'paged' => $paged,
'post_type' => 'earmarks',
'post_status' => 'publish',
	'orderby' => 'title',
	'meta_key' => 'subcommittee',
	'order' => 'ASC',
);

// get earmarks based on category
if ( ! empty( $category ) ) {
$args['category_name'] = $category;
$args['orderby'] = 'category_name subcommittee title';

	if ($category === "funded-earmarks") {
		$is_funded_page = true; 
	}
}

// get earmarks based on year
if ( ! empty( $year) ) {
$args['meta_query'] = array(
	'year_filter' => array(
	'key'	 	=> 'year',
	'value'	  	=> $year,
	'compare' 	=> '=',
	)
);
$args['orderby'] = 'year_filter subcommittee title';
}

// filter by subcommittee
if (isset($_POST['categoriesDropdown'])){
	$subcommittee = $_POST['categoriesDropdown'];
	$subcommittee_filter = array(
		'key'	  	=> 'subcommittee',
		'value'	  	=> $subcommittee,
		'compare' 	=> '=',
	);
	array_push($args['meta_query'], $subcommittee_filter);
}

$cert_letters = spruce_extension_get_cert_letters($year); // get cert letters based on year
$posts = new WP_Query($args);

ob_start();
if($posts->have_posts()) {
$current_subcommittee = "";
while($posts->have_posts()) {
	$posts->the_post();
	$subcommittee = get_field('subcommittee');
	$project_title = get_field('project_title');
	$requested_by = get_field('requested_by');
	$recipient_name = get_field('recipient_name');
	$project_purpose = get_field('project_purpose');
	$project_location = get_field('project_location');
	$amt_requested = get_field('amount_requested_by_the_senator');
	$amt_funded = get_field('amount_funded');
	
	// new subcommittee section
	if ($subcommittee != $current_subcommittee) {
		$current_subcommittee = $subcommittee;
		$cert_letter_url = $cert_letters[$subcommittee];
?>
<h3 style="margin-top: 2rem; color: #042B61; font-size: 22px !important; font-weight: bold;">
	<?php echo esc_html($subcommittee); ?></h3>
<a class="link-flash border-slim link-red" href="<?php echo esc_html($cert_letter_url); ?>" target="_blank">
	<b>Certification Letter Available Here</b>
</a>
<br><br>
<?php
		
	}  
?>

<div class="earmarks-project-container">
	<p><b>Project Title: </b><?php echo esc_html($project_title); ?></p>
	<?php if (esc_html($requested_by)): ?>
	<p><b>Requested By: </b><?php echo esc_html($requested_by); ?></p>
	<?php endif; ?>
	<p><b>Recipient Name: </b><?php echo esc_html($recipient_name); ?></p>
	<p><b>Project Purpose: </b><?php echo esc_html($project_purpose); ?></p>
	<p><b>Project Location: </b><?php echo esc_html($project_location); ?></p>
	<?php if (esc_html($amt_requested) && !$is_funded_page): ?>
	<p><b>Amount Requested by the Senator: </b><?php echo esc_html($amt_requested); ?></p>
	<?php endif; ?>
	<?php if (esc_html($amt_funded) && $is_funded_page): ?>
	<p><b>Amount Funded: </b><?php echo esc_html($amt_funded); ?></p>
	<?php endif; ?>
</div>
<?php
}
?>
<?php
} 
return ob_get_clean();
}

add_shortcode('sae-get-earmarks', 'spruce_extension_earmarks');
add_shortcode('sae-live-stream', 'spruce_extension_get_streams');

?>