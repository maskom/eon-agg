<?php
//auto share facebook\\
require_once 'facebook_php_sdk/src/Facebook/Facebook.php';
require_once 'facebook_php_sdk/src/Facebook/autoload.php';
//add_action('init', 'autoshare');
function autoshare(){


    $appId = '1328828393865821';
    $appSecret = 'af93952f86d954d45871f8f673a995f5';
    $pageId = '804274132933965';
    $pageAccessToken ='EAAS4jZChiQl0BAOFo2BoHAJAdUTWuMjYAjNuM88cy1sbvMVdM1SXZB0jmWgqay4qdvxs6BtifilHeV7BxuNPVHDml2osXJg9zIdZAzKKCd2Fi6hTW0kAn0wZC99ZAYgNd3Kod4eSZCy12MqF8tTuU4te1UJ7r3XvfJq0TObi1e3AZDZD';

    $fb = new Facebook\Facebook ([
        //demo_ewbnesia
        'app_id' => $appId,
        'app_secret' => $appSecret,
        'default_graph_version' => 'v2.9'
    ]);

    $args = array(
        'post_type'         => 'article',
        'post_status'      => 'publish',
        'posts_per_page'    => 1,
        'orderby'           => 'rand',
        'meta_key'          => 'share_fb',
        'meta_value'        => 'no',
        'meta_compare'      => '=='
    );

    $posts = new WP_Query( $args );
    if ( $posts->have_posts() ) {
        while ( $posts->have_posts() ) {$posts->the_post();
            $ID = get_the_ID();
            $title = get_the_title();
            $source_title = get_post_meta( $ID, 'source_title', true );
			$source_title = html_entity_decode($source_title, ENT_COMPAT, "UTF-8");
            $link = get_post_meta( $ID, 'news_url', true );
            $source = get_post_meta( $ID, 'source', true );
            $picture = get_post_meta( $ID, 'news_image', true );
            //$desc = wp_trim_words( get_the_content(), 15, '...' );
            $desc = get_the_content();
            $short_link = wp_get_shortlink($ID);
            $home_short_link = 'http://bit.ly/2owOlKe';
            //$message


            //Post property to Facebook
            $linkData = [
                'message'       => $source_title."\r\n\r\nSelengkapnya: ".$short_link."\r\nArtikel Lainnya:	 ".$home_short_link."\r\n",
                'link'          => $short_link,
                'picture'       => $picture,
                "name"          => $source_title,
                "caption"       => $source,
                "description"   => $desc
            ];

            try {
                 $response = $fb->post('/'.$pageId.'/feed', $linkData, $pageAccessToken);
                 update_post_meta( $post_id = $ID, $key = 'share_fb', $value = 'yes' );
            } catch(Facebook\Exceptions\FacebookResponseException $e) {
                echo 'Graph returned an error: '.$e->getMessage();
                exit;
            } catch(Facebook\Exceptions\FacebookSDKException $e) {
                echo 'Facebook SDK returned an error: '.$e->getMessage();
                exit;
            }
            $graphNode = $response->getGraphNode();


        }
    }

}
