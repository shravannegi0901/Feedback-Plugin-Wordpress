<?php

$path = preg_replace('/wp-content(?!.*wp-content).*/','',__DIR__);
include($path.'wp-load.php');
global $plugin_page;
global $wpdb;
$feedback_helpful = $_POST['feedback_helpful'];
$feedback = sanitize_textarea_field($_POST['feedback']);
$name = $_POST['name'];
$feedback_negative = $_POST['feedback_negative'];
if(empty($feedback_negative)){
	$feedback_negative = "helpful";
}
$post_id = sanitize_text_field($_POST['post_id']);
$email = sanitize_email($_POST['email']);
$newsletter_sub = $_POST['newsletter_sub'];
$wp_asin_data = array(
	'feedback_helpful' => $feedback_helpful,
	'feedback' => $feedback,
	'feedback_negative' => $feedback_negative,
	'name' => $name,
	'read_status' => 'no',
	'post_id' => $post_id,
	'type' => 'post',
	'email' => $email,
	'newsletter_sub' => $newsletter_sub,
	'created' => date("Y-m-d h:i:s"),
);
$wpdb->insert(''.$wpdb->prefix.'plugins_new_feedback', $wp_asin_data);
if($newsletter_sub == 'yes'){
	$pagination_count = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."plugins_link_setting");
	if(empty($pagination_count)){
		return false;
	}else{
		$apiKey = $pagination_count[0]->api_key;
    	$listId = $pagination_count[0]->list_key;
	}
	if(empty($apiKey) || empty($listId)){
		return false;
	}

    $memberId = md5(strtolower($data['email']));
    $dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
    $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listId . '/members/' . $memberId;

    $json = json_encode([
        'email_address' => $wp_asin_data['email'],
        'status'        => 'subscribed', // "subscribed","unsubscribed","cleaned","pending"
        'merge_fields'  => [
            'FNAME'     => $wp_asin_data['name'],
        ]
    ]);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);  
    $result = curl_exec($ch);
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
}
$msg = "done";
echo json_encode($msg);die;	
?>