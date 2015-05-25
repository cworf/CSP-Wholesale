<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/* Credits to http://hirizh.name/blog/styling-chat-transcript-for-custom-post-format/ */
function daoon_chat_post($content) {
    $chatoutput = "<div class=\"chat\">\n";
    $split = preg_split("/(\r?\n)+|(<br\s*\/?>\s*)+/", $content);
    foreach($split as $haystack) {
        if (strpos($haystack, ":")) {
            $string = explode(":", trim($haystack), 2);
            $who = strip_tags(trim($string[0]));
            $what = strip_tags(trim($string[1]));
            $row_class = empty($row_class)? " class=\"chat-highlight\"" : "";
            $chatoutput .= "<p><strong class=\"who\">$who:</strong> $what</p>\n";
        } else {
            $chatoutput .= $haystack . "\n";
        }
    }

    // print our new formated chat post
    $content = $chatoutput . "</div>\n";
    return $content;
}

global $apollo13;

echo '<h2 class="post-title"><a href="'. esc_url(get_permalink()) . '">' . get_the_title() . '</a></h2><i class="post-format-icon fa fa-comments-o"></i>';
?>

<div class="real-content">

    <?php echo daoon_chat_post($post->post_content);?>

    <div class="clear"></div>
</div>

<?php a13_post_meta(); ?>