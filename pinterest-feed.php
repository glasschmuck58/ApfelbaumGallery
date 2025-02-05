<?php
/*
Plugin Name: Pinterest Feed
Description: Zeigt einen Pinterest-Feed mit Bildern und Titeln an.
Version: 1.9
Author: Dein Name
*/

function pinterest_feed_enqueue_styles() {
    wp_enqueue_style('pinterest-feed-style', plugins_url('pinterest-feed.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'pinterest_feed_enqueue_styles');

function pinterest_feed_shortcode($atts) {
    $atts = shortcode_atts(array(
        'username' => 'dein_pinterest_username',
        'board' => 'dein_pinterest_board',
        'limit' => 10,
    ), $atts, 'pinterest_feed');

    $username = $atts['username'];
    $board = $atts['board'];
    $limit = $atts['limit'];

    $response = wp_remote_get("https://api.pinterest.com/v3/pidgets/boards/$username/$board/pins/");
    if (is_wp_error($response)) {
        return 'Fehler beim Abrufen des Pinterest-Feeds.';
    }

    $body = wp_remote_retrieve_body($response);

    $data = json_decode($body, true);

    if (empty($data['data']['pins'])) {
        return 'Keine Pins gefunden.';
    }

    $output = '<div class="pinterest-feed">';
    $count = 0;
    foreach ($data['data']['pins'] as $pin) {
        if ($count >= $limit) break;
        $image_url = isset($pin['images']['564x']['url']) ? $pin['images']['564x']['url'] : '';
        $description = isset($pin['description']) ? $pin['description'] : '';
        $output .= '<div class="pinterest-pin">';
        $output .= '<a href="https://de.pinterest.com/pin/'.$pin['id'].'/" target="_blank"><img src="' . esc_url($image_url) . '" alt="' . esc_attr($description) . '"></a>';
        $output .= '<p>' . esc_html($pin['description']) . '</p>';
        $output .= '<p><a href="https://wa.me/4917629350456?text=schmuckstueck"><img src="https://stephankrauss.de/wp-content/uploads/2025/02/whatsapp.jpg" style="float: left;"></a>&nbsp;&nbsp;<a href="mailto:glassschmuck@stephankrauss.de?subject=Testtext"><img src="https://stephankrauss.de/wp-content/uploads/2025/02/mail.jpg" style="float: left;"></a></p>';
        $output .= '</div>';
        $count++;
    }
    $output .= '</div>';

    return $output;
}

add_shortcode('pinterest_feed', 'pinterest_feed_shortcode');
?>
