<?php

/*
Plugin Name: Dinsoftware web
Plugin URI: https://premiumwp.ro
Description: Plugin pentru foodPress.
Author: Din Bogdan
Version: 1.0
Author URI: https://www.linkedin.com/in/dinbogdan/
*/


function din_include_myuploadscript() {
    /*
     * I recommend to add additional conditions just to not to load the scipts on each page
     * like:
     * if ( !in_array('post-new.php','post.php') ) return;
     */
    if ( ! did_action( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }

    wp_enqueue_script( 'myuploadscript', plugin_dir_url( __FILE__ )  . '/din.js', array('jquery'), null, false );
}

add_action( 'admin_enqueue_scripts', 'din_include_myuploadscript' );

function din_image_uploader_field( $name, $value = '') {
    $image = ' button">Upload image';
    $image_size = 'full'; // it would be better to use thumbnail size here (150x150 or so)
    $display = 'none'; // display state ot the "Remove image" button

    if( $image_attributes = wp_get_attachment_image_src( $value, $image_size ) ) {

        // $image_attributes[0] - image URL
        // $image_attributes[1] - image width
        // $image_attributes[2] - image height

        $image = '"><img src="' . $image_attributes[0] . '" style="max-width:95%;display:block;" />';
        $display = 'inline-block';

    }

    return '
    <div>
        <a href="#" class="din_upload_image_button' . $image . '</a>
        <input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" />
        <a href="#" class="din_remove_image_button" style="display:inline-block;display:' . $display . '">Remove image</a>
    </div>';
}

/*
 * Add a meta box
 */
add_action( 'admin_menu', 'din_meta_box_add' );

function din_meta_box_add() {
    add_meta_box('dindiv', // meta box ID
        'Brand', // meta box title
        'din_print_box', // callback function that prints the meta box HTML
        'menu', // post type where to add it
        'normal', // priority
        'high' ); // position
}

/*
 * Meta Box HTML
 */
function din_print_box( $post ) {
    $meta_key = 'second_featured_img';
    echo din_image_uploader_field( $meta_key, get_post_meta($post->ID, $meta_key, true) );
}

/*
 * Save Meta Box data
 */
add_action('save_post', 'din_save');

function din_save( $post_id ) {
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
        return $post_id;

    $meta_key = 'second_featured_img';

    update_post_meta( $post_id, $meta_key, $_POST[$meta_key] );

    // if you would like to attach the uploaded image to this post, uncomment the line:
    // wp_update_post( array( 'ID' => $_POST[$meta_key], 'post_parent' => $post_id ) );

    return $post_id;
}

// ajax results
add_action('init', 'process_post');
function process_post(){
    if(isset($_GET['menuid']) && $_GET['menuid'] > 0) {

        $menuimage = get_post_meta( $_GET['menuid'], 'second_featured_img', true);
       // echo $menuimage;
        if(empty($menuimage)){
          //  echo plugin_dir_url( __FILE__ ) . "a.jpg";
        }elseif( $image_attributes = wp_get_attachment_image_src( $menuimage, "thumbnail" ) ) {
                echo $image_attributes[0];
            }

        die();
        }
    }



add_action('wp_footer', 'add_this_script_footer');


function add_this_script_footer(){

    ?>

    <style>
     .fp_thumbnail .dinthumb {position: absolute; width: 15% !important;  top: 12px;}
     .xdinthumb {position: absolute;  width: 9% !important;  left: 85%;}



     /* top: 0px; */

    </style>

<script type="text/javascript">
    //<![CDATA[

    jQuery(document).ready(function() {



        jQuery(".menuItem.style_2").click(function() {
            if (jQuery(this).find('.dinthumb').length > 0){
                var imgsrc = jQuery(this).find('.dinthumb').attr('src');
                setTimeout(function () {
                    console.log('aaa eee dd' + imgsrc);
                    jQuery( '<img class="floatx xdinthumb" src="'+ imgsrc +'">' ).insertBefore( jQuery('body').find('.fp_pop_body .fp_details .fp_menu_type'));



                }, 2000);


            }


            });



        jQuery(".menuItem.style_2 ").each(function(){

            var menuid =  jQuery(this).attr('data-menuitem_id');
            var ulra = "?menuid=" + menuid;
            var selector = "[data-menuitem_id=" + menuid + "]";

            jQuery.ajax({url: ulra, success: function(result){

             //

                if(result){
//insertBefore(jQuery(selector).find('.fp_inner_box .fp_thumbnail'))
                    jQuery( '<img class="new_fp_thumb dinthumb" src="'+ result +'">' ).insertBefore(jQuery(selector).find('.fp_inner_box .fp_thumbnail .new_fp_thumb'));



                }else{

                }

            }});

        });


    });



    //]]>
</script>

    <?php } // end add_this_script_footer