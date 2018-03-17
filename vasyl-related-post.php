<?php
/*
Plugin Name: Related Post
Description: Show related post on your site.
Version:  1.0
Author: Vasyl Selivonenko
Author URI: https://github.com/Vasylevs
*/

add_filter('the_content', 'vasyl_related_post');
add_action('wp_enqueue_scripts', 'wp_register_style_scripts');

function wp_register_style_scripts(){
    wp_register_style( 'vasyl-style', plugins_url("/css/vasyl-style.css", __FILE__) );
    wp_register_script( 'vasyl-jquery-tools-js', plugins_url("/js/jquery.tools.min.js", __FILE__),array('jquery'));
    wp_register_script( 'vasyl-scripts-js', plugins_url("/js/vasyl-script.js", __FILE__),array('jquery'));

    wp_enqueue_script('vasyl-jquery-tools-js');
    wp_enqueue_script('vasyl-scripts-js');
    wp_enqueue_style('vasyl-style');
}

function vasyl_related_post($content){

    if(!is_single()) return $content;

    $id = get_the_ID();
    $categories = get_the_category($id);
    foreach ($categories as $category){
        $cats_id[] = $category->cat_ID;
    }

    $related_posts = new WP_Query(
        array(
            'posts_per_page' => 5,
            'category__in' => $cats_id,
            'post__not_in' => array($id),
            'orderby' => 'rand'
        )
    );

    if($related_posts->have_posts()){
        $content .= '<div class="related-posts"><h3>Возможно вас заинтересуют эти записи</h3>';

        while ($related_posts->have_posts()){
            $related_posts->the_post();

            if(has_post_thumbnail()){
                $img = get_the_post_thumbnail(get_the_ID(),array(100,100),array('alt' => get_the_title(), 'title' => get_the_title()));
            }else{
                $img = '<img src="'.plugins_url("images/no_img.jpg", __FILE__).'" alt="'.get_the_title().'" title="'.get_the_title().'" style="with:100px">';
            }

            $content .= '<a href="'.get_permalink().'">'.$img.'</a>';
        }

        $content .= '</div>';

        wp_reset_query();
    }

    return $content;
}


