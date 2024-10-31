<?php

/*
Plugin Name: wp-serpd
Version: 1.0
Plugin URI: http://www.serpd.com
Description: Adds a button to your Wordpress articles to offer and vote on serpd.com.
Author: Chris Burns
Author URI: http://www.websitedesign411.com
Modified version of the WP-Vote Plugin by serpd http://www.serpd.com
*/

$message = "";

if (!function_exists('serpd_request_handler')) {
    function serpd_request_handler() {
        global $message;

        if ($_POST['serpd_action'] == "update options") {
            $serpd_align_v = $_POST['serpd_align_sl'];

    		if(get_option("serpd_box_align")) {
    			update_option("serpd_box_align", $serpd_align_v);
    		} else {
    			add_option("serpd_box_align", $serpd_align_v);
    		}

            $message = '<br clear="all" /> <div id="message" class="updated fade"><p><strong>Option saved. </strong></p></div>';
        }
    }
}

if(!function_exists('serpd_add_menu')) {
    function serpd_add_menu () {
        add_options_page("serpd Options", "serpd Options", 8, basename(__FILE__), "serpd_displayOptions");
    }
}

if (!function_exists('serpd_displayOptions')) {
    function serpd_displayOptions() {

        global $message;
        echo $message;

		print('<div class="wrap">');
		print('<h2>serpd Options</h2>');

        print ('<form name="serpd_form" action="'. get_bloginfo("wpurl") . '/wp-admin/options-general.php?page=wp-serpd.php' .'" method="post">');
?>

		<p>Align:
        <select name="serpd_align_sl" id="serpd_align_sl">
			<option value="Top Left"   <?php if (get_option("serpd_box_align") == "Top Left") echo " selected"; ?> >Top Left</option>
			<option value="Top Right"   <?php if (get_option("serpd_box_align") == "Top Right") echo " selected"; ?> >Top Right</option>
			<option value="Bottom Left"  <?php if (get_option("serpd_box_align") == "Bottom Left") echo " selected"; ?> >Bottom Left</option>
			<option value="Bottom Right"  <?php if (get_option("serpd_box_align") == "Bottom Right") echo " selected"; ?> >Bottom Right</option>
			<option value="None"  <?php if (get_option("serpd_box_align") == "None") echo " selected"; ?> >None</option>
		</select><br /><br /> </p>

<?php
		print ('<p><input type="submit" value="Save &raquo;"></p>');
		print ('<input type="hidden" name="serpd_action" value="update options" />');
		print('</form></div>');

    }
}


if (!function_exists('serpd_serpdhtml')) {
	function serpd_serpdhtml($float) {
		global $wp_query;
		$post = $wp_query->post;
		$permalink = get_permalink($post->ID);
        $title = urlencode($post->post_title);
		$serpdhtml = <<<CODE

    <span style="margin: 0px 6px 0px 0px; float: $float;">

	<script type="text/javascript">
	submit_url = "$permalink";
	</script>
    <script type="text/javascript" src="http://www.serpd.com/index.php?page=evb"></script>
	</span>
CODE;
	return  $serpdhtml;
	}
}


if (!function_exists('serpd_addbutton')) {
	function serpd_addbutton($content) {

		if ( !is_feed() && !is_page() && !is_archive() && !is_search() && !is_404() ) {
    		if(! preg_match('|<!--serpd-->|', $content)) {
    		    $serpd_align = get_option("serpd_box_align");
    		    if ($serpd_align) {
                    switch ($serpd_align) {
                        case "Top Left":
        		              return serpd_serpdhtml("left").$content;
                              break;
                        case "Top Right":
        		              return serpd_serpdhtml("Right").$content;
                              break;
                        case "Bottom Left":
        		              return $content.serpd_serpdhtml("left");
                              break;
                        case "Bottom Right":
        		              return $content.serpd_serpdhtml("right");
                              break;
                        case "None":
        		              return $content;
                              break;
                        default:
        		              return serpd_serpdhtml("left").$content;
                              break;
                    }
                } else {
        		      return serpd_serpdhtml("left").$content;
                }

    		} else {
                  return str_replace('<!--serpd-->', serpd_serpdhtml(""), $content);
            }
        } else {
			return $content;
        }
	}
}

if (!function_exists('show_serpd')) {
	function show_serpd($float = "left") {
        global $post;
		$permalink = get_permalink($post->ID);
		echo <<<CODE

    <span style="margin: 0px 6px 0px 0px; float: $float;">

	<script type="text/javascript">
	submit_url = "$permalink";
	</script>
    <script type="text/javascript" src="http://www.serpd.com/index.php?page=evb"></script>
	</span>
CODE;
    }
}

add_filter('the_content', 'serpd_addbutton', 999);
add_action('admin_menu', 'serpd_add_menu');
add_action('init', 'serpd_request_handler');

?>