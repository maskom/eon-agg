<?php

function meta_tags () {

    if(is_single() || is_singular('article')) {
        $ID = get_the_ID();
        $title = get_the_title($ID);
        $source_title = get_post_meta( $ID, 'source_title', true );
        $source_title = html_entity_decode($source_title, ENT_COMPAT, "UTF-8");
        $link = get_post_meta( $ID, 'news_url', true );
        $source = get_post_meta( $ID, 'source', true );
        $picture = get_post_meta( $ID, 'news_image', true );
        $desc = wp_trim_words( get_the_content($ID), 20, '...' );

        ?>


        <meta name="description" content="<?php echo $desc ?>" />
        <meta property="fb:pages" content="804274132933965" />
        <meta property="fb:app_id" content="1328828393865821" />
        <meta property="og:site_name" content="<?php echo get_bloginfo(); ?>"/>
        <meta property="og:type" content="article" />
        <meta property="og:title" content="<?php echo $source_title ?>"/>
        <meta property="og:image" content="<?php echo $picture; ?>"/>
        <meta property="og:description" content="<?php echo $desc ?>"/>
        <meta property="og:url" content="<?php echo $link; ?>"/>
        <meta name="twitter:card" content="summary_large_image"/>
        <meta name="twitter:description" content=""<?php echo $desc ?>" />
        <meta name="twitter:image" content="<?php echo $picture; ?>"/>
        <meta name="twitter:image:src" content="<?php echo $picture; ?>"/>
        <meta name="twitter:title" content="<?php echo $source_title ?>" />
        <link rel="image_src" href="<?php echo $picture; ?>" />


    <?php } else {
        return;
    }

}

add_action('wp_head', 'meta_tags', 5);
