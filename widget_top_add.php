<?php
/*
Plugin Name: Simple Taxonomy Widget
Description: An easy to use plugin to show a Taxonomy in a widget area. 
Author: Arif Ansari / Impressive Solutions
Version: 1.2
Author URI: http://www.miracle.co.uk
License: GPLv2 or later
*/

/*

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

function top_widgets_register_widgets() {
		register_widget( 'TopWidget' );
	}
add_action( 'widgets_init', 'top_widgets_register_widgets' );

class TopWidget extends WP_Widget {
    
    private $taxonomy_name='ad_cat';

	function TopWidget() {
		$widget_ops = array( 'classname' => ' top-sellers', 'description' => 'Display posts from Featured Add Data' );
		$this->WP_Widget( 'TopWidget-widget', 'Simple Featured Add Widget', $widget_ops );
	}


	function widget( $args, $instance ) {
		extract($args);

		echo $before_widget;

		if( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title', esc_attr( $instance['title'] ) ) . $after_title;

		$order = 'title' == $instance['order_by'] ? 'ASC' : 'DESC';
       $query = new WP_Query( array ( 'post_type' => 'ad_listing', 'orderby' => 'meta_value', 'meta_key' => 'cp_price' ,'meta_value' => '100', 'meta_compare' => '>=' ) );
        // print_r($query);
           print  '<ul>';
         while ( $query->have_posts() ) {
		$query->the_post();
        
        $args = array(
   'post_type' => 'attachment',
   'numberposts' => 1,
   'post_status' => null,
   'post_parent' => get_the_id(),
  );

  $attachments = get_posts( $args );
//  print_r($attachments);
    $attachment =$attachments[0];

         $get_price = get_post_meta( get_the_ID(),'cp_price' );

                          ?>
                                <li>
                                    <a href="<?php the_permalink(); ?>">
                                    <?php
                                         if ( $attachment ) {
           echo wp_get_attachment_image( $attachment->ID, 'full' );
     }else
     {
        echo "<img style='border:1px solid #000' src='http://dubai.impression.me.uk/wp-content/themes/classipress/images/no-thumb.jpg' >";
     }
     ?></a>
                                    <h5><?php  echo substr(get_the_content(),0,60).'...'; ?></h5>
                                    <h5><strong>AED <?php echo $get_price[0]; ?></strong></h5>
                                </li>
                            <?php    
           }
           
           print  '</ul>';

		wp_reset_postdata();

		echo $after_widget;
	}
    
    
    private function Dropdown_submenu($termchildren) 
    {
        $output_html='';
        foreach ( $termchildren as $child ) {
	       $term = get_term_by( 'id', $child, $this->taxonomy_name );
        	$output_html.='<li><a href="' . get_term_link( $child, $this->taxonomy_name ) . '">' . $term->name . '</a></li>';
            }
        
        
        return $output_html;
        
    }

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['select'] = strip_tags($new_instance['select']);
		$instance['order_by'] = strip_tags($new_instance['order_by']);
		$instance['f_image'] = strip_tags($new_instance['f_image']);
		$instance['img_width'] = strip_tags($new_instance['img_width']);
		$instance['num_of_posts'] = strip_tags($new_instance['num_of_posts']);
		return $instance;
	}
	function form( $instance ) {

		// Defaults
		$defaults = array( 'title' => '', 'select' => '', 'order_by' => '', 'f_image' => '', 'img_width' => '', 'num_of_posts' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults ); 

		// Escape Settings
		$title = esc_attr($instance['title']);
		$select = esc_attr($instance['select']);
		$order_by = esc_attr($instance['order_by']);
		$f_image = esc_attr($instance['f_image']);
		$img_width = esc_attr($instance['img_width']);
		$num_of_posts = esc_attr($instance['num_of_posts']);
		?>

		<p>
	      	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?></label>
	      	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
	    </p>

	<?php
	}
}
