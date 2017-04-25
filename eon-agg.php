<?php
/**
 * Plugin Name: eonnet aggregator
 * Plugin URI: http://eonnet.com
 * Description: eonnet aggregator
 * Version: 1.0
 * Author: maskom
 * Author URI: http://eonnet.com
 */

/*add_action('admin_menu', 'test_plugin_setup_menu');

function test_plugin_setup_menu(){
    add_menu_page( 'Test Plugin Page', 'Test Plugin', 'manage_options', 'test-plugin', 'test_init' );
}*/


require_once 'custom-type.php';
require_once 'autoshare.php';
require_once 'metadata.class.php';
require_once 'meta-tags.php';



$fetch_data[] = array();
function getRss($rss,$catID, $user) {
    $urls = '';
    $rss_url = $rss;
    $api_endpoint = 'https://api.rss2json.com/v1/api.json?rss_url=';
    $data = json_decode( file_get_contents($api_endpoint . urlencode($rss_url)) , true );

    if($data['status'] == 'ok'){
        $c=0;
        foreach ($data['items'] as $key => $item) {
            $urls[] = $item['link'];
            $c++;
        }
        for($i=0;$i<$c;$i++) {
            $url = $urls[$i];
            $url = htmlspecialchars_decode($url);
            $metaData = MetaData::fetch($url);
            foreach ($metaData as $key => $value) {
                $fetch_data[$key] = $value;
            }
            //var_dump($fetch_data);
            $title = $fetch_data['og:title'];
			$title_article = '[Article] '.$title;
            $siteName = $fetch_data['og:site_name'];
            $image = '';
            $image_1 = $fetch_data['og:image'];
            $image_2 = $fetch_data['og:image'][0];
            if (strlen($image_2)>1) {
                $image = $image_2;
            } else {
                $image = $image_1;
            }
            $desc = $fetch_data['og:description'];
            $urlNews = $fetch_data['og:url'];
            $newsKeywords = '';
            if (array_key_exists('news_keywords', $fetch_data)) {
                $newsKeywords = $fetch_data['news_keywords'];
            } else {
                $newsKeywords = $fetch_data['keywords'];
            }
            preg_match('@^(?:http://|https://)?([^/]+)@i',
                $urlNews, $matches);
            $host = $matches[1];
			
			$sApiUrl = 'http://www.google.com/s2/favicons?domain='.$host;
            $favicon = $sApiUrl;

            // Initialize the post ID to -1. This indicates no action has been taken.
            $post_id = -1;
            // If the page doesn't already exist, then create it
            if( null == get_page_by_title( $title, OBJECT, 'post' ) ) {
                $args = array(
                    'post_author'       =>  $user,
                    'comment_status'	=>	'closed',
                    'ping_status'		=>	'closed',
                    'post_title'		=>	$title,
                    'post_content'      =>  $desc,
                    'post_status'		=>	'publish',
                    'post_type'		    =>	'post'
                );
                // Set the page ID so that we know the page was created successfully
                $post_id = wp_insert_post($args);
                //category
                wp_set_post_terms( $post_id, array($catID), 'category' );
                //tags
                if($newsKeywords){
                    wp_set_post_tags( $post_id, $newsKeywords, true );
                }
                //custom field
                if($image) {
                    add_post_meta($post_id, 'news_image', $image, true);
                }
                if($urlNews) {
                    add_post_meta($post_id, 'news_url', $urlNews, true);
                }
                if ($siteName) {
                    add_post_meta($post_id, 'source', $siteName, true);
                }
                if ($host){
                    add_post_meta($post_id, 'host', $host, true);
                }
				if ($favicon) {
                    add_post_meta($post_id, 'favicon', $favicon, true);
                }
                add_post_meta($post_id, 'share_fb', 'no', true);
                add_post_meta($post_id, 'share_tw', 'no', true);

                // Otherwise, we'll stop and set a flag
            } else {
                // Arbitrarily use -2 to indicate that the page with the title already exists
                $post_id = -2;
            } // end if

            if( null == get_page_by_title( $title_article, OBJECT, 'article' ) ) {
                $args = array(
                    'post_author'       =>  $user,
                    'comment_status'	=>	'closed',
                    'ping_status'		=>	'closed',
                    'post_title'		=>	$title_article,
                    'post_content'      =>  $desc,
                    'post_status'		=>	'publish',
                    'post_type'		    =>	'article'
                );
                // Set the page ID so that we know the page was created successfully
                $post_id = wp_insert_post($args);
                //category
                wp_set_post_terms( $post_id, array($catID), 'category' );
                //tags
                if($newsKeywords){
                    wp_set_post_tags( $post_id, $newsKeywords, true );
                }
                //custom field
                if($image) {
                    add_post_meta($post_id, 'news_image', $image, true);
                }
                if($urlNews) {
                    add_post_meta($post_id, 'news_url', $urlNews, true);
                }
                if ($siteName) {
                    add_post_meta($post_id, 'source', $siteName, true);
                }
                if ($host){
                    add_post_meta($post_id, 'host', $host, true);
                }
				if ($favicon) {
                    add_post_meta($post_id, 'favicon', $favicon, true);
                }
                add_post_meta($post_id, 'share_fb', 'no', true);
                add_post_meta($post_id, 'share_tw', 'no', true);
                add_post_meta($post_id, 'source_title', $title, true);

                // Otherwise, we'll stop and set a flag
            } else {
                // Arbitrarily use -2 to indicate that the page with the title already exists
                $post_id = -2;
            } // end if

        }

    }

};


function get_crazball($rss,$user) {
    $urls = '';
    $rss_url = $rss;
    $api_endpoint = 'https://api.rss2json.com/v1/api.json?rss_url=';
    $data = json_decode( file_get_contents($api_endpoint . urlencode($rss_url)) , true );

    if($data['status'] == 'ok'){
        $c=0;
        foreach ($data['items'] as $key => $item) {
            $urls[] = $item['link'];
            $c++;
        }
        for($i=0;$i<$c;$i++) {
            $url = $urls[$i];
            $url = htmlspecialchars_decode($url);
            $metaData = MetaData::fetch($url);
            foreach ($metaData as $key => $value) {
                $fetch_data[$key] = $value;
            }
            //var_dump($fetch_data);
            $title = $fetch_data['og:title'];
            $title_article = '[Article] '.$title;
            $title_crazball = '[Bola] '.$title;
            $siteName = $fetch_data['og:site_name'];
            $image = '';
            $image_1 = $fetch_data['og:image'];
            $image_2 = $fetch_data['og:image'][0];
            if (strlen($image_2)>1) {
                $image = $image_2;
            } else {
                $image = $image_1;
            }
            $desc = $fetch_data['og:description'];
            $urlNews = $fetch_data['og:url'];
            $newsKeywords = '';
            if (array_key_exists('news_keywords', $fetch_data)) {
                $newsKeywords = $fetch_data['news_keywords'];
            } else {
                $newsKeywords = $fetch_data['keywords'];
            }
            preg_match('@^(?:http://|https://)?([^/]+)@i',
                $urlNews, $matches);
            $host = $matches[1];

            $sApiUrl = 'http://www.google.com/s2/favicons?domain='.$host;
            $favicon = $sApiUrl;



            if( null == get_page_by_title( $title_crazball, OBJECT, 'crazball' ) ) {
                $args = array(
                    'post_author'       =>  $user,
                    'comment_status'	=>	'closed',
                    'ping_status'		=>	'closed',
                    'post_title'		=>	$title_crazball,
                    'post_content'      =>  $desc,
                    'post_status'		=>	'publish',
                    'post_type'		    =>	'crazball'
                );
                // Set the page ID so that we know the page was created successfully
                $post_id = wp_insert_post($args);
                //category
                //wp_set_post_terms( $post_id, array($catID), 'category' );
                //tags
                if($newsKeywords){
                    wp_set_post_tags( $post_id, $newsKeywords, true );
                }
                //custom field
                if($image) {
                    add_post_meta($post_id, 'news_image', $image, true);
                }
                if($urlNews) {
                    add_post_meta($post_id, 'news_url', $urlNews, true);
                }
                if ($siteName) {
                    add_post_meta($post_id, 'source', $siteName, true);
                }
                if ($host){
                    add_post_meta($post_id, 'host', $host, true);
                }
                if ($favicon) {
                    add_post_meta($post_id, 'favicon', $favicon, true);
                }
                add_post_meta($post_id, 'share_fb', 'no', true);
                add_post_meta($post_id, 'share_tw', 'no', true);
                add_post_meta($post_id, 'source_title', $title, true);

                // Otherwise, we'll stop and set a flag
            } else {
                // Arbitrarily use -2 to indicate that the page with the title already exists
                $post_id = -2;
            } // end if
        }

    }

};

/*add_action('init','post_insert');
function post_insert(){
    $rssKaskus = 'https://www.kaskus.co.id/rss';
    $catkaskus = 2;
    $status = 'active';
    getRss($rssKaskus, $catkaskus);
}*/

//intervals
add_filter( 'cron_schedules', 'intervals_cron' );
function intervals_cron( $schedules ) {
    $schedules['ten_minutes'] = array(
        'interval' => 600, // Number of seconds, 600 in 10 minutes
        'display'  => __( 'Every 10 minutes' ),
    );
    $schedules['thirty_minutes'] = array(
        'interval' => 1800, // Number of seconds, 600 in 30 minutes
        'display'  => __( 'Every 30 minutes' ),
    );
    $schedules['two_hours'] = array(
        'interval' => 7200,
        'display'  => __( 'Every 2 hours' ),
    );
   /* $schedules['six_hours'] = array(
        'interval' => 21600,
        'display'  => __( 'Every 6 hours' ),
    );
    $schedules['seven_hours'] = array(
        'interval' => 25200,
        'display'  => __( 'Every 7 hours' ),
    );
    $schedules['eight_hours'] = array(
        'interval' => 28800,
        'display'  => __( 'Every 8 hours' ),
    );*/
    return $schedules;
}

add_action( 'insert_ten_minutes', 'getRss_insertPost_ten_minutes' );
function getRss_insertPost_ten_minutes() {
    $rssKaskus = 'https://www.kaskus.co.id/rss';
    $catkaskus = 2;
    getRss($rssKaskus,$catkaskus,1);
};

add_action( 'insert_two_hours', 'getRss_insertPost_two_hours' );
function getRss_insertPost_two_hours() {
    $rssKaskus = 'https://www.kaskus.co.id/rss';
    $catkaskus = 2;
    getRss($rssKaskus,$catkaskus,1);
};

add_action( 'insert_cloter_1', 'getRss_insertPost_cloter_1' );
function getRss_insertPost_cloter_1() {
    $rssNgakak = 'https://news.google.com/news?cf=all&hl=id&pz=1&ned=id_id&q=Ngakak&output=rss';
    $rssTerkini = 'https://news.google.com/news?cf=all&hl=id&pz=1&ned=id_id&q=Terkini&output=rss';
    $rssDiskon = 'https://news.google.com/news?cf=all&hl=id&pz=1&ned=id_id&q=Diskon&output=rss';
    $rssGadget = 'https://news.google.com/news?cf=all&hl=id&pz=1&ned=id_id&q=Gadget&output=rss';
    $rssViral = 'https://news.google.com/news?cf=all&hl=id&pz=1&ned=id_id&q=Viral&output=rss';
    $rssTravel = 'https://news.google.com/news?cf=all&hl=id&pz=1&ned=id_id&q=Travel&output=rss';
	
	$catNgakak = 3;
	$catTerkini = 4;
	$catDiskon = 5;
	$catGadget = 6;
	$catViral = 7;
	$catTravel = 8;
	
	getRss($rssNgakak,$catNgakak,1);
	getRss($rssTerkini,$catTerkini,1);
	getRss($rssDiskon,$catDiskon,1);
	getRss($rssGadget,$catGadget,1);
	getRss($rssViral,$catViral,1);
	getRss($rssTravel,$catTravel,1);


	//crazball
    $rssCrazball = 'https://news.google.com/news?cf=all&hl=id&pz=1&ned=id_id&q=Sepak+bola&output=rss';
    get_crazball($rssCrazball,1);
};

add_action( 'insert_cloter_2', 'getRss_insertPost_cloter_2' );
function getRss_insertPost_cloter_2() {
    $rssBisnis = 'https://news.google.com/news?cf=all&hl=id&pz=1&ned=id_id&topic=b&output=rss';
    $rssTeknologi = 'https://news.google.com/news?cf=all&hl=id&pz=1&ned=id_id&topic=tc&output=rss';
    $rssHiburan = 'https://news.google.com/news?cf=all&hl=id&pz=1&ned=id_id&topic=e&output=rss';
    $rssOlahraga = 'https://news.google.com/news?cf=all&hl=id&pz=1&ned=id_id&topic=s&output=rss';
    $rssSains = 'https://news.google.com/news?cf=all&hl=id&pz=1&ned=id_id&topic=snc&output=rss';
    $rssKesehatan = 'https://news.google.com/news?cf=all&hl=id&pz=1&ned=id_id&topic=m&output=rss';
	
	$catBisnis = 9;
	$catTeknologi = 10;
	$catHiburan = 11;
	$catOlahraga = 12;
	$catSains = 13;
	$catKesehatan = 14;
	
	getRss($rssBisnis,$catBisnis,1);
	getRss($rssTeknologi,$catTeknologi,1);
	getRss($rssHiburan,$catHiburan,1);
	getRss($rssOlahraga,$catOlahraga,1);
	getRss($rssSains,$catSains,1);
	getRss($rssKesehatan,$catKesehatan,1);
};

add_action( 'insert_cloter_3', 'getRss_insertPost_cloter_3' );
function getRss_insertPost_cloter_3() {
    $rssFilm = 'https://news.google.com/news?cf=all&hl=id&pz=1&ned=id_id&q=Film&output=rss';
    $rssPoitik = 'https://news.google.com/news?cf=all&hl=id&pz=1&ned=id_id&q=Politik&output=rss';
    $rssGames = 'https://news.google.com/news?cf=all&hl=id&pz=1&ned=id_id&q=Games&output=rsss';
    $rssSmartphone = 'https://news.google.com/news?cf=all&hl=id&pz=1&ned=id_id&q=Smartphone&output=rss';
    $rssKuliner = 'https://news.google.com/news?cf=all&hl=id&pz=1&ned=id_id&q=Kuliner&output=rss';
    $rssSocialMedia = 'https://news.google.com/news?cf=all&hl=id&pz=1&ned=id_id&q=Social+Media&output=rss';
    $rssInternasional = 'https://news.google.com/news?cf=all&hl=id&pz=1&ned=id_id&topic=w&output=rss';
	
	$catFilm = 15;
	$catPoitik = 16;
	$catGames = 17;
	$catSmartphone = 18;
	$catKuliner = 19;
	$catSocialMedia = 20;
	$catInternasional = 21;
	
	getRss($rssFilm,$catFilm,1);
	getRss($rssPoitik,$catPoitik,1);
	getRss($rssGames,$catGames,1);
	getRss($rssSmartphone,$catSmartphone,1);
	getRss($rssKuliner,$catKuliner,1);
	getRss($rssSocialMedia,$catSocialMedia,1);
	getRss($rssInternasional,$catInternasional,1);
};

add_action('share_fb', 'share_fb_thirty_minutes');
function share_fb_thirty_minutes() {
    autoshare();
}

// Add function to register event to WordPress init
add_action( 'init', 'post_rss_cron');
// Function which will register the event
function post_rss_cron() {
    // Make sure this event hasn't been scheduled
    if( !wp_next_scheduled( 'insert_two_hours' ) ) {
        // Schedule the event
        wp_schedule_event( time(), 'two_hours', 'insert_two_hours' );
    }
	if( !wp_next_scheduled( 'insert_cloter_1' ) ) {
        // Schedule the event
        wp_schedule_event( time(), 'twicedaily', 'insert_cloter_1' );
    }
	if( !wp_next_scheduled( 'insert_cloter_2' ) ) {
        // Schedule the event
        wp_schedule_event( time(), 'daily', 'insert_cloter_2' );
    }
	if( !wp_next_scheduled( 'insert_cloter_3' ) ) {
        // Schedule the event
        wp_schedule_event( time(), 'daily', 'insert_cloter_3' );
    }
    if( !wp_next_scheduled( 'share_fb' ) ) {
        // Schedule the event
        wp_schedule_event( time(), 'thirty_minutes', 'share_fb' );
    }
}

