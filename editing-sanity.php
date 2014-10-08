<?php

namespace EditingSanity;

add_filter('use_default_gallery_style', function () { return false; });
add_filter('img_caption_shortcode_width', function ( $w, $a, $c ) { return ''; }, 10, 3);
add_action('admin_enqueue_scripts', 'EditingSanity\admin_typekit_enqueue');
add_action('admin_head', 'EditingSanity\admin_typekit_inline');

add_action('init', 'EditingSanity\init');
add_filter('mce_buttons', 'EditingSanity\style_select');
add_filter('tiny_mce_before_init', 'EditingSanity\styles_dropdown');
add_filter('image_size_names_choose', 'EditingSanity\insertable_image_sizes');
add_filter('the_content', 'EditingSanity\remove_ptags_around_images');

function init() {
  add_editor_style( 'editor-style.css' );
}

function admin_typekit_enqueue($hook) {
  if ($hook !== 'post.php' && $hook !== 'post-new.php') {
    return;
  }
  wp_enqueue_script('ey_admin_typekit', '//use.typekit.net/' . TYPEKIT_ID . '.js', false, false, false);
}

function admin_typekit_inline() {
  if (wp_script_is('ey_admin_typekit', 'done')) {
    echo '<script>try{Typekit.load();}catch(e){}</script>';
  }
}

function insertable_image_sizes($sizes) {
   unset($sizes['thumbnail']);
   unset($sizes['full']);
   return $sizes;
}

function style_select( $buttons ) {
  array_unshift($buttons, 'styleselect');
  return $buttons;
}

function styles_dropdown( $settings ) {
  $styles = array(
    array('title' => 'Regular paragraph',  'format' => 'p', 'classes' => 'p1'),
    array('title' => 'Lede',  'block' => 'p', 'classes' => 'lede'),
    array('title' => 'Heading 3',  'format' => 'h3'),
    array('title' => 'Heading 4',  'format' => 'h4'),
  );

  $settings['style_formats_merge'] = false;
  $settings['style_formats'] = json_encode($styles);
  return $settings;
}

function remove_ptags_around_images($content){
   $content = preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
   $content = preg_replace('/<p class="\w*">\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
   return $content;
}
