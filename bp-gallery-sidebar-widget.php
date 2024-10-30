<?php
/*
Plugin Name: BP-Gallery Sidebar Widget
Plugin URI: http://crimsoncurtain.com/sites/wpdev
Description: A widget to show random images from BP-Gallery with links to the iamge's gallery.
Author: Phillip Bryan 
Version: 1.2
Author URI: http://crimsoncurtain.com/sites/wpdev
*/ 

/*
 * Changelog
 * --------- 

 * 1.2 - Added use of gallery slug, so that thumbails link to their galleries instead of their owner profiles.
 * 1.1 - Modified for WP-MU compatability.  Now references the BP Gallery tables using the wpdb->base_prefix variable.
 
*/
add_action('plugins_loaded', 'init_bp_gallery_sidebar_widget'); 
function init_bp_gallery_sidebar_widget() { 
	if (BP_GALLERY_IS_INSTALLED) {
        register_sidebar_widget('BP-Gallery Sidebar Widget', 'bp_gallery_sidebar_widget');
        register_widget_control('BP-Gallery Sidebar Widget', 'bp_gallery_sidebar_widget_control');
    }
}

function bp_gallery_sidebar_widget($args) {

    global $wpdb;
	extract($args);   
	$required_status = 'public';

	$options = bp_gallery_sidebar_widget_get_options();

    switch($options['gallery_order']) {
        case 'added_desc':
            $order = 'id DESC';
            break;
        case 'added_asc':
            $order = 'id ASC';
            break;
        default:
            $order = 'RAND()';
            break;
    }

    $results = $wpdb->get_results("SELECT id, cover_mini, creator_id, slug FROM ".$wpdb->base_prefix."bp_gallery_galleries WHERE status='$required_status' ORDER BY ".$order);
	// At first I used a LIMIT clause set to the maximum number of galleries.  But empty galleries resulted in a shortened list.
	// I've removed the LIMIT and tested for the max within the loop.  
	// Q: Does this make the SQL query inefficient by forcing WP to read all the records?  
	$count = 0;
    if (is_array($results) && count($results) > 0) {
        $galleries = array();
        foreach($results as $result) {
		
			// Use the gallery cover pic if there is one 
            if ($options['gallery_thumbnail'] == 'cover' && (int)$result->cover_mini > 0) {
				// We are good to go.
	
			} elseif ($options['gallery_thumbnail'] == 'random') {
            	$result->cover_mini = $wpdb->get_var("SELECT local_thumb_path FROM ".$wpdb->base_prefix."bp_gallery_media WHERE status='$required_status' AND type='photo' AND gallery_id='".$result->id."' ORDER BY RAND() LIMIT 1");        
            } 
			
			// take the first image if we don't have one yet
			if (($options['gallery_thumbnail'] == 'first') || (!$result->cover_mini)) {
            	$result->cover_mini = $wpdb->get_var("SELECT local_thumb_path FROM ".$wpdb->base_prefix."bp_gallery_media WHERE status='$required_status' AND gallery_id = '" . $result->id . "' ORDER BY sort_order ASC, id ASC LIMIT 1");
			}

			if ($result->cover_mini) {    
                $galleries[] = $result;
				if (++$count == $options['max_galleries']) break;
            }
        }        
		
	   $image_style = '<div'. ($options['output_padding'] ? ' style="padding-right:'.$options['output_padding'].'px; padding-bottom:'.$options['output_padding'].'px;float:left;"' : '').'>'; 

        if(count($galleries) > 0) {
            $title = $options['title'];
			            
            $output = "\n";
            $output .= $before_widget . "\n";
            $output .= $before_title . $title . $after_title . "\n";
            
            foreach($galleries as $gallery) {				
				$username = $wpdb->get_var("SELECT user_login FROM ".$wpdb->base_prefix."users WHERE ID = '" . $gallery->creator_id . "' LIMIT 1");	

				$gallery_link = get_bloginfo('url').'/members/'.$username.'/gallery/my-galleries/'.$gallery->slug;
			   
				$output .= $image_style.'<a href="' . $gallery_link . '">';                
              	$output .= '<img src="' . site_url() .'/'.  $gallery->cover_mini . '" title="' . $gallery->title . ' by '.$username.'" alt="' . $gallery->title . '" width="' . $options['output_width'] . '" height="' . $options['output_height'] . '"/>';               
                $output .= '</a></div>';
            }
          
            $output .= '<br style="clear: both" />';
            $output .= "\n" . $after_widget . "\n";
            echo $output;
        }
    } 
}

function bp_gallery_sidebar_widget_control() {

    $options = bp_gallery_sidebar_widget_get_options();      

    if($_POST['bp_gallery_sidebar_widget-submit']){
      
	  $options['title'] = htmlspecialchars($_POST['bp_gallery_sidebar_widget-title']);
      $options['max_galleries'] = $_POST['bp_gallery_sidebar_widget-max_galleries'];
      $options['gallery_order'] = $_POST['bp_gallery_sidebar_widget-gallery_order'];
      $options['gallery_thumbnail'] = $_POST['bp_gallery_sidebar_widget-gallery_thumbnail'];
      $options['output_width'] = $_POST['bp_gallery_sidebar_widget-output_width'];
      $options['output_height'] = $_POST['bp_gallery_sidebar_widget-output_height'];
      $options['output_padding'] = $_POST['bp_gallery_sidebar_widget-output_padding'];
      update_option('bp_gallery_sidebar_widget', $options);
    }    

    switch($options['gallery_order']) {
        case 'random':
        case 'added_asc':
        case 'added_desc':
            break;
        default:
            $options['gallery_order'] = 'random';
            break;
    }   

    switch($options['gallery_thumbnail']) {
        case 'preview':
        case 'first':
        case 'random':
            break;
        default:
            $options['gallery_thumbnail'] = 'preview';
            break;
    }  

    echo '<p><label for="bp_gallery_sidebar_widget-title">Widget Title</label>
        <input type="text" id="bp_gallery_sidebar_widget-title" name="bp_gallery_sidebar_widget-title" value="' . $options['title'] . '" />
        </p>

        <p><label for="bp_gallery_sidebar_widget-max_galleries">Maximum Galleries</label>
              <input type="text" id="bp_gallery_sidebar_widget-max_galleries" size="3" name="bp_gallery_sidebar_widget-max_galleries" value="' . $options['max_galleries'] . '" />
        </p>

        <p><label for="bp_gallery_sidebar_widget-gallery_order">Gallery Order</label>
              <select id="bp_gallery_sidebar_widget-gallery_order" name="bp_gallery_sidebar_widget-gallery_order">';              

                  echo '<option value="random"';
                  echo ($options['gallery_order'] == 'random') ? ' selected="selected">' : '>';
                  echo 'Random</option>';                 

                  echo '<option value="added_asc"';
                  echo ($options['gallery_order'] == 'added_asc') ? ' selected="selected">' : '>';
                  echo 'Earliest</option>';                  

                  echo '<option value="added_desc"';
                  echo ($options['gallery_order'] == 'added_desc') ? ' selected="selected">' : '>';
                  echo 'Latest</option>';                 

              echo '</select>
        </p>
		
        <p>
              <label for="bp_gallery_sidebar_widget-gallery_thumbnail">Thumbnail</label>
              <select id="bp_gallery_sidebar_widget-gallery_thumbnail" name="bp_gallery_sidebar_widget-gallery_thumbnail">';
              
                  echo '<option value="cover"';
                  echo ($options['gallery_thumbnail'] == 'cover') ? ' selected="selected">' : '>';
                  echo 'Cover image</option>';                  

                  echo '<option value="first"';
                  echo ($options['gallery_thumbnail'] == 'first') ? ' selected="selected">' : '>';
                  echo 'First image</option>';

                  echo '<option value="random"';
                  echo ($options['gallery_thumbnail'] == 'random') ? ' selected="selected">' : '>';
                  echo 'Random image</option>';                  

              echo '</select>
        </p>

        <p>
              <label for="bp_gallery_sidebar_widget-output_width">Thumbnail width</label>
              <input type="text" id="bp_gallery_sidebar_widget-output_width" size="4" name="bp_gallery_sidebar_widget-output_width" value="' . $options['output_width'] . '" />
        </p>    

        <p>
              <label for="bp_gallery_sidebar_widget-output_height">Thumbnail height</label>
              <input type="text" id="bp_gallery_sidebar_widget-output_height" size="4" name="bp_gallery_sidebar_widget-output_height" value="' . $options['output_height'] . '" />
        </p>
		
        <p><label for="bp_gallery_sidebar_widget-output_padding">Thumbnail padding</label>
              <input type="text" id="bp_gallery_sidebar_widget-output_padding" size="4" name="bp_gallery_sidebar_widget-output_padding" value="' . $options['output_padding'] . '" />
      
		 <input type="hidden" id="bp_gallery_sidebar_widget-submit" name="bp_gallery_sidebar_widget-submit" value="1" /></p>';
}

function bp_gallery_sidebar_widget_get_options() {

	$options = get_option('bp_gallery_sidebar_widget');   

	if (!is_array($options)) {
		$options = array(

            'title' => 'Galleries',
            'max_galleries' => 6,
            'gallery_order' => 'random',
            'gallery_thumbnail' => 'cover',
            'output_width' => 100,
            'output_height' => 75,
            'output_padding' => 0
        );
	}

    return $options;
}