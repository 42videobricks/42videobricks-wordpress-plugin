<?php

use Api42Vb\Client\Model\VideoMultipartUploadFinalize;
use Api42Vb\Client\Model\VideoMultipartUploadFinalizePartsInner;
use ApiVideo\Client\Client;

// Get client function
function videobrickswp_verify_request()
{
    require_once VIDEOBRICKS_PLUGIN_DIR . 'vendor/autoload.php';

    $apikey = get_option("videobricks_api_key");
    $env = get_option("videobricks_env");
    $host = getHostByEnv($env);
    $config = Api42Vb\Client\Configuration::getDefaultConfiguration();
    $config->setApiKey('x-api-key', $apikey);
    $config->setHost($host);

    $apiInstance = new Api42Vb\Client\Api\VideosApi(
        new GuzzleHttp\Client(),
        $config
    );
    try {
        $apiInstance->getVideos();
        $response = TRUE;
    }
    catch (\Api42Vb\Client\ApiException $e) {
        $response = FALSE;
    }
    return $response;
}

function videobrickswp_get_videos($limit=null, $offset=null, $search = null)
{
    require_once VIDEOBRICKS_PLUGIN_DIR . 'vendor/autoload.php';

    $apikey = get_option("videobricks_api_key");
    $env = get_option("videobricks_env");
    $host = getHostByEnv($env);
    $config = Api42Vb\Client\Configuration::getDefaultConfiguration();
    $config->setApiKey('x-api-key', $apikey);
    $config->setHost($host);

    $apiInstance = new Api42Vb\Client\Api\VideosApi(
        new GuzzleHttp\Client(),
        $config
    );
    try {
        $videos = $apiInstance->getVideos($limit, $offset, $search);
        return $videos->getData();
    }
    catch (\Api42Vb\Client\ApiException $e) {
       return [];
    }
}


// function for generate shortcode
function videobrickswp_shortcode($atts)
{
    $variable = null;
    $videoId = $atts['id'];
    $variable .= '<iframe width="640" height="480" frameborder="0" style="border: 0" src="https://stream.42videobricks.com/'. $videoId .'/player" allow="encrypted-media" allowfullscreen></iframe>';
    return $variable;
}
add_shortcode('42videobricks', 'videobrickswp_shortcode');

// register css
function videobrickswp_include_css()
{
    wp_register_style('videobricks-style', plugins_url('../assets/style.css', __FILE__));
    wp_enqueue_style('videobricks-style');
}
add_action('admin_init', 'videobrickswp_include_css');
add_action('wp_ajax_video_creation', 'video_creation' );
add_action("wp_ajax_nopriv_video_creation", "video_creation");

add_action('wp_ajax_video_finalize', 'video_finalize' );
add_action("wp_ajax_nopriv_video_finalize", "video_finalize");

function video_creation()
{
    require_once VIDEOBRICKS_PLUGIN_DIR . 'vendor/autoload.php';

    $apikey = get_option("videobricks_api_key");
    $env = get_option("videobricks_env");
    $host = getHostByEnv($env);
    $config = Api42Vb\Client\Configuration::getDefaultConfiguration();
    $config->setApiKey('x-api-key', $apikey);
    $config->setHost($host);

    $apiInstance = new Api42Vb\Client\Api\VideosApi(
        new GuzzleHttp\Client(),
        $config
    );
    try {
        $videoName = $_POST['name'];
        $types = wp_check_filetype($_POST['name']);
        $mymeTypes = [
            //avi
            "video/x-msvideo",
            "video/msvideo",
            "video/avi",
            "application/x-troff-msvideo",
            //mov
            "video/quicktime",
            //mp4
            "video/mp4",
            //mpeg
            "video/mpeg",
            //mpg
            "video/mpg",
            //mxf
            "application/mxf",
            //ts
            "video/MP2T"
        ];
        if (!in_array($types['type'], $mymeTypes)) {
             wp_send_json('File format not supported', 400);
        }
        $size = $_POST['size'];
        $data['title'] = stripslashes($videoName);
        $data['public'] = TRUE;
        $videoProps = new \Api42Vb\Client\Model\VideoProperties($data);
        $videoResponse = $apiInstance->addVideo($videoProps);
        $id = $videoResponse->getId();
        $data = new \Api42Vb\Client\Model\VideoMultipartUploadInit(['name' => $videoName, 'size' => $size]);
        $multiParts = $apiInstance->initMultipartUploadVideoById($id, $data);
        $response['response'] = $multiParts->jsonSerialize();
        $response['videoId'] = $id;
         wp_send_json($response, 200);
    }
    catch (\Api42Vb\Client\ApiException $e) {
         wp_send_json($e->getMessage(), $e->getCode());
    }
}

function video_finalize()
{
    require_once VIDEOBRICKS_PLUGIN_DIR . 'vendor/autoload.php';

    $apikey = get_option("videobricks_api_key");
    $env = get_option("videobricks_env");
    $host = getHostByEnv($env);
    $config = Api42Vb\Client\Configuration::getDefaultConfiguration();
    $config->setApiKey('x-api-key', $apikey);
    $config->setHost($host);

    $apiInstance = new Api42Vb\Client\Api\VideosApi(
        new GuzzleHttp\Client(),
        $config
    );
    try {

        $fileId = $_POST['fileId'];
        $fileKey = $_POST['fileKey'];
        $parts = $_POST['parts'];
        $videoId = $_POST['videoId'];
        foreach ($parts as $delta => $part) {
            $part['PartNumber'] = (int) $part['PartNumber'];
            $partObject = new VideoMultipartUploadFinalizePartsInner();
            $partObject->setPartNumber($part['PartNumber']);
            $partObject->setETag($part['ETag']);
            $finalParts[$delta] = $partObject;
        }
        $model = new VideoMultipartUploadFinalize(['file_id' => $fileId, 'file_key' => $fileKey, 'parts' => $finalParts]);
        $apiInstance->finalizeMultipartUploadVideoById($videoId, $model);
    }
    catch (\Api42Vb\Client\ApiException $e) {
         wp_send_json($e->getMessage(), $e->getCode());
    }
    wp_send_json('Video created with ID : '. $videoId, 200);
}

// register javascript
function videobrickswp_scripts()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('videobricks_script', plugins_url('../assets/script.js', __FILE__));
    wp_localize_script( 'videobricks_script', 'videobricks', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
}
add_action('admin_enqueue_scripts', 'videobrickswp_scripts');

function getHostByEnv($env) {
    switch ($env) {
        case "sandbox":
            $host = "https://api-sbx.42videobricks.com";
            break;
        case "staging":
            $host = "https://api-stg.42videobricks.com";
            break;
        case "production":
            $host = "https://api.42videobricks.com";
            break;
        default:
            $host = "https://api-sbx.42videobricks.com";
    }
    return $host;
}