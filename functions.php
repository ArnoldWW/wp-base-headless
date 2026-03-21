<?php

/* -- Theme Setup -- */
function coffee_shop_setup()
{
    // Enable support for post thumbnails (featured images)
    add_theme_support("post-thumbnails");
}
add_action('after_setup_theme', 'coffee_shop_setup');

/* -- REST API Initialization -- */
function coffee_shop_api_init()
{
    // Add featured image URL to REST API response for pages
    register_rest_field(array("page"), "featured_images", [
        "get_callback" => "get_featured_image_sizes",
    ]);
}

// Helper function to retrieve featured image URLs in various sizes
function get_featured_image_sizes($post)
{
    if (!$post["featured_media"]) {
        return null; // No featured image
    }

    $image_sizes = get_intermediate_image_sizes();

    foreach ($image_sizes as $size) {

        //Skip sizes that are not relevant
        if (in_array($size, ["2048x2048"])) {
            continue;
        }

        $image = wp_get_attachment_image_src($post["featured_media"], $size);

        $images[$size === "1536x1536" ? "full" : $size] = [
            "url"   => $image[0],
            "width" => $image[1],
            "height" => $image[2]
        ];
    }

    // Return all image URLs
    return $images;
}
add_action('rest_api_init', 'coffee_shop_api_init');
