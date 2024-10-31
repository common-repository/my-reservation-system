<?php
/*
Plugin Name: My Reservation System
Plugin URI: https://myreservationsystem.com
Description: My Reservation System is an Online Reservation System that allows businesses or individual to embed their availability calendar, Take Online Reservations, Receive Online Payments. This plugin connects your WordPress site to your My Reservation System account, and provides a powerful real-time booking interface - right within your existing WordPress site.
Author: My Reservation System
Version: 2.3
Author URI: https://myreservationsystem.com
*/
define('my_reservation_system_VERSION', '2.3');
define('my_reservation_system_URL', plugins_url('', __FILE__));
class my_reservation_system_widget extends WP_Widget { 
	## Initialize
    var $instance_opt;
	function __construct() {
        $widget_ops = array('classname' => 'widget_my_reservation_system', 'description' => __("My Reservation System WordPress Plugin, All-in-one Real-time Reservation System and Booking Manager ", 'mrs'));
        $control_ops = array('height' => 500);
        parent::WP_Widget('my_reservation_system', __('My Reservation System', 'mrs'), $widget_ops, $control_ops);
        if (is_admin()) {            
            $mrs = get_option('widget_my_reservation_system');
            if (is_array($mrs) && !isset($mrs['_varscleaned'])) {
                $toRep = array('mrs_', 'is_', 'diable_post');
                $repWith = array('', 'hide_', 'hide_in_posts');
                foreach ($mrs as $k => $v) {
                    if (is_array($v)) {   
                    	foreach ($v as $m => $n) {  
                    		$old = $m;
                            $new = str_replace($toRep, $repWith, $old);
                            $mrs[$k][$new] = $mrs[$k][$old];
                            unset($mrs[$k][$old]);
                        }
                    }
                }
                $mrs['_varscleaned'] = true;
                update_option('widget_my_reservation_system', $mrs);
            }
            if ((is_array($mrs) && !isset($mrs['_version'])) || $mrs['_version'] != my_reservation_system_VERSION) {                $mrs['_version'] = my_reservation_system_VERSION;
                update_option('widget_my_reservation_system', $mrs);
            }
        }
    }
    function mrs_page_check($instance) {
        $hide_single = $instance['hide_single'];
        $hide_archive = $instance['hide_archive'];
        $hide_home = $instance['hide_home'];
        $hide_page = $instance['hide_page'];
        $hide_search = $instance['hide_search'];
        if (is_home() == 1 && $hide_home != 1) {   
        	return true;
        } elseif (is_single() == 1 && $hide_single != 1) {    
        	return true;
        } elseif (is_page() == 1 && $hide_page != 1) {       
        	return true;
        } elseif (is_archive() == 1 && $hide_archive != 1) {  
        	return true;
        } elseif (is_tag() == 1 && $hide_archive != 1) {     
        	return true;
        } elseif (is_search() == 1 && $hide_search != 1) {    
        	return true;
        } else {    
        	return false;
        }
    }
    function mrs_admin_check($instance) {
        $hide_admin = $instance['hide_admin'];
        if (current_user_can('level_10') && $hide_admin == 1) {
            return true;
        } else {
            return false;
        }
    }
    function mrs_hide_post_check($instance) {
        $hide_in_posts = $instance['hide_in_posts'];
        $splitId = explode(',', $hide_in_posts);
        if (is_page($splitId) || is_single($splitId)) {
            return false;
        } else {
            return true;
        }
    }
    function mrs_show_post_check($instance) {
        $show_in_posts = $instance['show_in_posts'];
        $splitId = explode(',', $show_in_posts);
        if (is_page($splitId) || is_single($splitId)) {
            return true;
        } else {
            return false;
        }
    }
    function mrs_all_ok($instance) {
        if((int)$instance['display_widget'] == 1){
        	return false;
        	}
    	if (!is_home() && !is_admin()) {            
            $args = array('status' => 'hold', 'post_id' => get_the_ID());
            $comments = get_comments($args);
            foreach ($comments as $comment) {
                $comment_cont = $comment->comment_content;
                $a = $comment_cont;
                if (stripos($comment_cont, '[myreservationwidgets_' . $instance["code"] . ']') 
                	|| $comment_cont == '[myreservationwidgets_' . $instance["code"] . ']') {
                    return false;
                }
            }
        }
        if ($this->mrs_admin_check($instance)) { 
        	return false;
        } else {     
            if ($instance['display_in'] == 'all') { 
            	return true;
            } elseif ($instance['display_in'] == 'hide_only') {
                return ($this->mrs_page_check($instance) && $this->mrs_hide_post_check($instance));
            } elseif ($instance['display_in'] == 'show_only') {     
            	return ($this->mrs_show_post_check($instance));
            } else {      
            	return true;
            }
        }
    }
    function mrs_all_ok_content($content) {        
        $instance = $this->instance_opt;
        if(isset($instance['code'])){
        if (stripos($content, '[myreservationwidgets_' . $instance['code'] . ']')
        	|| $content == '[myreservationwidgets_' . $instance['code'] . ']'){
                return false;
        }
        return true;
        }
        return true;
    }
    ## Display the Widget
    function widget($args, $instance) { 
    	$this->instance_opt = $instance;
    	extract($args);
       	if (!$this->mrs_all_ok($instance)) {
            return '';
        }
         if (!is_home() && !is_admin()) {
        $content = get_the_content();
        if(!$this->mrs_all_ok_content($content)){
        return '';
        }
         }
        if (empty($instance['title'])) { 
        	$title = '';
        } else {    
        	$title = $before_title . apply_filters('widget_title', $instance['title'], $instance, $this->id_base) . $after_title;
        }
        if (empty($instance['content'])) {   
        	$content = '';
        } elseif ($instance['add_para'] == 1) {      
        	$content = wpautop($instance['content']);
        } else {      
        	$content = $instance['content'];
        }
        $content = do_shortcode($content);
        if ($instance['type'] == 1) {   
        	$script = "<div id='div_freebc_software' data-background='".$instance['bgcolor']."' data-cid='".$instance['cid']."' data-local='".$instance['lang']."' data-room='auto' > </div><script src='https://myreservationsystem.com/js/freebc.js' async defer ></script>";
        }
        if ($instance['type'] == 2) {      
        	$script = "<div id='div_mrbc_software' data-background='".$instance['bgcolor']."' data-cid='".$instance['cid']."' data-local='".$instance['lang']."' data-room='".$instance['room']."' > </div><script src='https://myreservationsystem.com/js/mrbc.js' async defer ></script>";
        }
        
        $before_content = "\n" . '<div class="mrswidget textwidget" >' . "\n";
		$after_content = "\n" . '</div>' . "\n";
		$before_cmt = "\n<!-- Start - My Reservation System plugin v" . my_reservation_system_VERSION . " -->\n";
		$after_cmt =  "<!-- End - My Reservation System plugin v" . my_reservation_system_VERSION . " -->\n";
		## Output
		$output_content =
			$before_cmt .
				$before_widget . 
					$title . 
					$before_content . 
						$script . 
					$after_content . 
				$after_widget.
			$after_cmt;
		echo $output_content;
    }
    ## Save settings
    function update($new_instance, $old_instance) {  
    	$instance = $old_instance;
        $instance['title'] = stripslashes($new_instance['title']);
        $instance['content'] = stripslashes($new_instance['content']);
        if ($instance['code'] == "") {       
        	$instance['code'] = uniqid();
        } 
        $instance['cid'] = $new_instance['cid'];
        $instance['type'] = $new_instance['type'];
        $instance['display_widget'] = $new_instance['display_widget'];
        $instance['theme'] = $new_instance['theme'];
        $instance['view'] = $new_instance['view'];
        $instance['lang'] = $new_instance['lang'];
        $instance['bgcolor'] = str_replace('#', '', $new_instance['bgcolor']);
        $instance['room'] = $new_instance['room'];
        $instance['layout'] = $new_instance['layout'];
        $instance['hide_single'] = $new_instance['hide_single'];
        $instance['hide_archive'] = $new_instance['hide_archive'];
        $instance['hide_home'] = $new_instance['hide_home'];
        $instance['hide_page'] = $new_instance['hide_page'];
        $instance['hide_search'] = $new_instance['hide_search'];
        $instance['add_para'] = $new_instance['add_para'];
        $instance['hide_admin'] = $new_instance['hide_admin'];
        $instance['hide_in_posts'] = $new_instance['hide_in_posts'];
        $instance['show_in_posts'] = $new_instance['show_in_posts'];
        $instance['display_in'] = $new_instance['display_in'];
        return $instance;
    }
    ## MRS Widget form
    function form($instance) {   
    	$instance = wp_parse_args((array)$instance, array('title' => 'Book-Now', 'content' => '', 'cid' => '', 'view' => 'auto', 'lang' => '1', 'bgcolor' => '', 'room' => 'auto', 'display_widget' => '1', 'type' => '1', 'theme' => '', 'layout' => '1', 'code' => '', 'hide_single' => '0', 'hide_archive' => '0', 'hide_home' => '0', 'hide_page' => '0', 'hide_search' => '0', 'add_para' => '0', 'hide_admin' => '0', 'hide_in_posts' => '', 'show_in_posts' => '', 'display_in' => 'all'));
        $title = htmlspecialchars($instance['title']);
        $content = htmlspecialchars($instance['content']);
        $code = $instance['code'];
        $cid = $instance['cid'];
        $view = $instance['view'];
        $lang = $instance['lang'];
        $layout = $instance['layout'];
        $bgcolor = $instance['bgcolor'];
        $room = $instance['room'];
        $type = $instance['type'];
        $theme = $instance['theme'];
        $hide_single = $instance['hide_single'];
        $hide_archive = $instance['hide_archive'];
        $hide_home = $instance['hide_home'];
        $hide_page = $instance['hide_page'];
        $hide_search = $instance['hide_search'];
        $add_para = $instance['add_para'];
        $hide_admin = $instance['hide_admin'];
        $hide_in_posts = $instance['hide_in_posts'];
        $show_in_posts = $instance['show_in_posts'];
        $display_in = $instance['display_in'];
        $display_widget = $instance['display_widget'];
        ?>
		<div class="section">
			<ul class="mrsToolbar clearfix">
				<li class="mrsSupport" title="Visit Website"><a href="https://myreservationsystem.com/" target="_blank" class="mrsDonate"><img src="<?php echo my_reservation_system_URL . '/images/web-icon.png'; ?>" /></a></li>
				<li class="mrsSupport mrsShare" title="Like this plugin !"><img src="<?php echo my_reservation_system_URL . '/images/like-icon.png'; ?>" /></li>
				<li><img src="<?php echo my_reservation_system_URL . '/images/help-icon.png'; ?>" /> Help
					<ul>
						<li><a href="https://myreservationsystem.com/index/wordpress-plugin/booking-calendar" target="_blank">Documentation</a></li>
						<li><a href="https://myreservationsystem.com/index.php?page=index/contactus" target="_blank">Report Bugs</a></li>
					</ul>
				</li>
				<?php $plugin_url = my_reservation_system_URL; ?>
				<li class="mrsTb-preview" onclick='get_mrs_pop_up("#<?php echo $this->get_field_id('cid'); ?>", "#<?php echo $this->get_field_id('view'); ?>", "#<?php echo $this->get_field_id('lang'); ?>", "#<?php echo $this->get_field_id('bgcolor'); ?>", "#<?php echo $this->get_field_id('room'); ?>",  "<?php echo $plugin_url; ?>", "#<?php echo $this->get_field_id('type'); ?>", "#<?php echo $this->get_field_id('layout'); ?>", "#<?php echo $this->get_field_id('theme'); ?>")' editorId="<?php echo $this->get_field_id('content'); ?>"><img src="<?php echo my_reservation_system_URL . '/images/preview-icon.png'; ?>" /> Preview</li>
			</ul>
			<textarea rows="10" style="display:none" id="<?php echo $this->get_field_id('content'); ?>" name="<?php echo $this->get_field_name('content'); ?>" class="mrsContent" spellcheck="false" placeholder="Enter your HTML/Javascript/Plain text content here">
			</textarea>
		</div>
		<div class="section">
			<h3><?php _e("Settings", 'mrs'); ?></h3>
			<div class="section">
			<label><?php _e('Title', 'mrs'); ?> :<br />
				<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" class="widefat" placeholder="Enter the title here"/>
			</label>
		</div>
			<div class="section">
			<label>Calendar ID :<br />
				<input id="<?php echo $this->get_field_id('cid'); ?>" name="<?php echo $this->get_field_name('cid'); ?>" type="text" value="<?php echo $cid; ?>" class="widefat" placeholder="Enter the Calendar ID here"/>
			</label>
		</div>
		<div class="section">
			<label>Calendar Type :  <br />
				<select id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>"  class="widefat">
				<option   value="1" <?php if ($type == 1) {echo "selected";} ?> >Free Booking Calendar</option>
				<option value="2" <?php if ($type == 2) {echo "selected"; } ?> >Multi-room Booking Calendar</option>
				</select>
			</label>
		</div>
			<div class="section" id="MRS_section_room_number">
			<label>Room Number (to display the menu of room complete with "auto" ) :<br />
				<input id="<?php echo $this->get_field_id('room'); ?>" name="<?php echo $this->get_field_name('room'); ?>" type="text" value="<?php echo $room; ?>" class="widefat" placeholder="Enter the Room Number here"/>
			</label>
		</div>
		<div class="section" id="MRS_section_view_size">
			<label>Display Widget :  <br />
				<select id="<?php echo $this->get_field_id('display_widget'); ?>" name="<?php echo $this->get_field_name('display_widget'); ?>"  class="widefat">
				<option   value="1" <?php if ((int)$display_widget == 1) {echo "selected"; } ?> >Hide Widget</option>
				<option value="2" <?php if ((int)$display_widget == 2) {echo "selected"; } ?> >Show Widget</option>
				</select>
			</label>
		</div>
		<div class="section" id="MRS_section_view_size">
			<label>View (to display responsive widget complete with "auto" ) :<br />
				<input id="<?php echo $this->get_field_id('view'); ?>" name="<?php echo $this->get_field_name('view'); ?>" type="text" value="<?php echo $view; ?>" class="widefat" placeholder="Enter the View number here"/>
			</label>
		</div>
		<div class="section">
			<label>Language :<br />
				<input id="<?php echo $this->get_field_id('lang'); ?>" name="<?php echo $this->get_field_name('lang'); ?>" type="text" value="<?php echo $lang; ?>" class="widefat" placeholder="Enter the lang number here"/>
			</label>
		</div>
		<div class="section" id="MRS_section_background">
			<label>Background color (Special for Daily-Nightly Calendar): <br />
				<input id="<?php echo $this->get_field_id('bgcolor'); ?>" name="<?php echo $this->get_field_name('bgcolor'); ?>" type="text" value="<?php echo "#" . $bgcolor; ?>" class="color-field widefat" placeholder="Enter the background color here without #"/>
			</label>
		</div>
			<label class="mrsAccord">
			<input type="radio" name="<?php echo $this->get_field_name('display_in'); ?>" value="all" <?php echo ($display_in == 'all') ? 'checked="checked"' : ''; ?> /> <?php _e("Show in all pages", 'mrs'); ?></label>
			<label class="mrsAccord">
			<input type="radio" name="<?php echo $this->get_field_name('display_in'); ?>" value="show_only" <?php echo ($display_in == 'show_only') ? 'checked="checked"' : ''; ?> /> <?php _e("Show only in specific pages", 'mrs'); ?></label>
			<div class="mrsAccordWrap" <?php echo ($display_in != 'show_only') ? 'style="display:none"' : ''; ?> >
				<label><input id="<?php echo $this->get_field_id('show_in_posts'); ?>" type="text" name="<?php echo $this->get_field_name('show_in_posts'); ?>" value="<?php echo $show_in_posts; ?>" class="widefat mrsGetPosts"/></label>
				<span class="smallText"><?php _e("Post ID / name / title separated by comma", 'mrs'); ?></span>
			</div> <!-- MRS ACCORD WRAP 2 -->
			<label class="mrsAccord"><input type="radio" name="<?php echo $this->get_field_name('display_in'); ?>" value="hide_only" <?php echo ($display_in == 'hide_only') ? 'checked="checked"' : ''; ?> /> <?php _e("Hide only in specific pages", 'mrs'); ?></label>
			<div class="mrsAccordWrap" <?php echo ($display_in != 'hide_only') ? 'style="display:none"' : ''; ?> >
			<label><input id="<?php echo $this->get_field_id('hide_single'); ?>" type="checkbox"  name="<?php echo $this->get_field_name('hide_single'); ?>" value="1" <?php echo $hide_single == "1" ? 'checked="checked"' : ""; ?> /> <?php _e("Don't display in Posts page", 'mrs'); ?></label>
			<label><input id="<?php echo $this->get_field_id('hide_page'); ?>" type="checkbox" name="<?php echo $this->get_field_name('hide_page'); ?>" value="1" <?php echo $hide_page == "1" ? 'checked="checked"' : ""; ?>/> <?php _e("Don't display in Pages", 'mrs'); ?></label>
			<label><input id="<?php echo $this->get_field_id('hide_archive'); ?>" type="checkbox" name="<?php echo $this->get_field_name('hide_archive'); ?>" value="1" <?php echo $hide_archive == "1" ? 'checked="checked"' : ""; ?>/> <?php _e("Don't display in Archive or Tag page", 'mrs'); ?></label>
			<label><input id="<?php echo $this->get_field_id('hide_home'); ?>" type="checkbox" name="<?php echo $this->get_field_name('hide_home'); ?>" value="1" <?php echo $hide_home == "1" ? 'checked="checked"' : ""; ?>/> <?php _e("Don't display in Home page", 'mrs'); ?></label>
			<label><input id="<?php echo $this->get_field_id('hide_search'); ?>" type="checkbox" name="<?php echo $this->get_field_name('hide_search'); ?>" value="1" <?php echo $hide_search == "1" ? 'checked="checked"' : ""; ?>/> <?php _e("Don't display in Search page", 'mrs'); ?></label><br />
			<label><?php _e("Don't show in posts", 'mrs'); ?><br />
			<input id="<?php echo $this->get_field_id('hide_in_posts'); ?>" type="text" name="<?php echo $this->get_field_name('hide_in_posts'); ?>" value="<?php echo $hide_in_posts; ?>" class="widefat mrsGetPosts" placeholder="<?php _e("Post ID / name / title separated by comma", 'mrs'); ?>"/></label>
			</div><!-- MRS Accord 3 -->
			<div class="mrsOtherOpts">
			<label style="display:none">
			<input id="<?php echo $this->get_field_id('add_para'); ?>" type="checkbox" name="<?php echo $this->get_field_name('add_para'); ?>" value="1" checked="checked"  /> <?php _e("Automatically add paragraphs", 'mrs'); ?>
			</label> 
			<label>
			<input id="<?php echo $this->get_field_id('hide_admin'); ?>" type="checkbox" name="<?php echo $this->get_field_name('hide_admin'); ?>" value="1" <?php echo $hide_admin == "1" ? 'checked="checked"' : ""; ?>/> <?php _e("Don't display to admin", 'mrs'); ?>
			</label>
		    <br />
			</div> 
			<div class="mrsAccord" >
			<p class="mrstitle">Embedded My Reservation System:</p>
			<p class="mrsdescription">Create a Wordpress post or page and past your unique shortcode where you want the booking software to appear or in a hidden comment: 
			</p>
			<p class="mrsuniquecode">[myreservationwidgets_<?php echo $code; ?>]</p>
			</div>
			<input id="<?php echo $this->get_field_id('code'); ?>" name="<?php echo $this->get_field_name('code'); ?>" type="hidden" value="<?php echo $code; ?>" />
		    </div>
		    <br >
		<?php
    }
    function wpsr_check() {    
    	if (function_exists('wp_socializer') && WPSR_VERSION >= '2.3') {    
    		return 1;
        } else {         
        	return 0;
        }
    }
}
## End class
function my_reservation_system_custom_styles() {   
	wp_enqueue_style('calendar', my_reservation_system_URL . '/calendar.css');
}
add_action('wp_enqueue_scripts', 'my_reservation_system_custom_styles');
function my_reservation_system_init_widgets() { 
	register_widget('my_reservation_system_widget');
}
add_action('widgets_init', 'my_reservation_system_init_widgets');
function my_reservation_system_replace_content($content) {  
	$chek = new my_reservation_system_widget();
    $mrs = get_option('widget_my_reservation_system');
    $instance = $mrs[3];
    if (is_home()){
        	if (stripos($content, '[myreservationwidgets_' . $instance['code'] . ']')){
        	$content = str_replace('[myreservationwidgets_' . $instance['code'] . ']', "", $content);
        	return $content;
        	}
        }
    if (!is_home() && !is_admin()) {   
        $display = false;
        if ($instance['display_in'] == 'all') { 
            	  $display = true;
               } elseif ($instance['display_in'] == 'hide_only' && !$chek->mrs_hide_post_check($instance) && !$chek->mrs_page_check($instance)) {
               $display = false;
               } elseif ($instance['display_in'] == 'show_only' && $chek->mrs_show_post_check($instance)) {     
            	$display = true;
               } else {      
            	$display = false;
          }
        if ($instance['type'] == 1) {   
        	$script = "<div id='div_freebc_software' data-background='".$instance['bgcolor']."' data-cid='".$instance['cid']."' data-local='".$instance['lang']."' data-room='auto' > </div><script src='https://myreservationsystem.com/js/freebc.js' async defer ></script>";
        }
        if ($instance['type'] == 2) {      
        	$script = "<div id='div_mrbc_software' data-background='".$instance['bgcolor']."' data-cid='".$instance['cid']."' data-local='".$instance['lang']."' data-room='".$instance['room']."' > </div><script src='https://myreservationsystem.com/js/mrbc.js' async defer ></script>";
        }
       
        if (stripos($content, '[myreservationwidgets_' . $instance['code'] . ']')){
        	if($display){
        	 $content = str_replace('[myreservationwidgets_' . $instance['code'] . ']', "<p>".$script."</p>", $content);
        	  return $content; 
        	}else{
        	$content = str_replace('[myreservationwidgets_' . $instance['code'] . ']', "", $content);
        	}
        }
        $args = array('status' => 'hold', 'post_id' => get_the_ID());
        $comments = get_comments($args);
        if (!is_array($comments)) {  
        	return $content;
        } 
       $inside = false;
        foreach ($comments as $comment) {            
        	$comment_cont = $comment->comment_content;
            if (stripos($comment_cont, '[myreservationwidgets_' . $instance['code'] . ']') || $comment_cont == '[myreservationwidgets_' . $instance['code'] . ']') {                
            	$inside = true;
            }
        }
        if($inside && $display){
        $content = "<p>".$script."</p>".$content;
        }
        return $content;
        } else {
                return $content;
       }
}
add_filter('the_content', 'my_reservation_system_replace_content');
function my_reservation_system_include_files($hook) {
    if ($hook == "widgets.php") { 
    	wp_register_script('mrs-script', my_reservation_system_URL . '/js/mrs-widget.js');
        wp_enqueue_script('mrs-script');
        wp_register_script('mrs-awquicktag', my_reservation_system_URL . '/js/awQuickTag.js');
        wp_enqueue_script('mrs-awquicktag');
        wp_register_style('mrs-style', my_reservation_system_URL . '/mrs-widget-css.css');
        wp_enqueue_style('mrs-style');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('jquery');
    }
}
add_action('admin_enqueue_scripts', 'my_reservation_system_include_files');
function my_reservation_system_admin_footer() {   
	global $pagenow, $post;
    if ($pagenow == "widgets.php") {     
    	echo '<div id="msr_diplay_preview" >
    	      <div class="mrs_window_prview" >
    	      <span class="mrs_overlay_close"></span>
    	      <h3 class="mrs_window_head">Preview</h3>
    	      <div id="msr_height_div_43556">
    	      <iframe id="mrs_iframe_preview" src="about:blank" frameborder="0" scrolling="no"  ></iframe>
			  </div>
			  Support and tutorial <a href="https://myreservationsystem.com/" target="_blank">My Reservation System</a>
			  </div>
			  </div>';
    }
}
add_action('admin_footer', 'my_reservation_system_admin_footer');
## Action Links
function my_reservation_system_plugin_actions($links, $file) {    static $this_plugin;
    global $mrs_donate_link;
    if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
    if ($file == $this_plugin) {     
    	$settings_link = "<a href='https://myreservationsystem.com/' title='Visit Website' target='_blank'>Visit Website </a>";
        $links = array_merge(array($settings_link), $links);
    }
    return $links;
}
add_filter('plugin_action_links', 'my_reservation_system_plugin_actions', 10, 2);
?>