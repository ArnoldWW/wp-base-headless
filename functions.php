<?php

// Format ACF REST API response to show image URLs instead of attachment IDs
add_filter("acf/settings/rest_api_format", function () {
    return "standard";
});

/* -- Action Theme Setup -- */
function coffee_shop_setup()
{
    // Enable support for post thumbnails (featured images)
    add_theme_support("post-thumbnails");
}
add_action('after_setup_theme', 'coffee_shop_setup');

/* -- Action REST API -- */
function coffee_shop_api_init()
{
    // Add featured image URL to REST API response for pages
    register_rest_field(array("page", "post"), "featured_images", [
        "get_callback" => "get_featured_image_sizes",
    ]);

    // Add details of categories to REST API response for posts
    register_rest_field("post", "category_details", [
        "get_callback" => function ($post) {
            $categories = get_the_category($post["id"]);
            $category_details = [];
            foreach ($categories as $category) {
                $category_details[] = [
                    "name" => $category->name,
                    "slug" => $category->slug,
                ];
            }
            return $category_details;
        },
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

// Initialize REST API custom fields
add_action('rest_api_init', 'coffee_shop_api_init');
