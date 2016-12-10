<?php
// parameters
$hubVerifyToken = 'TOKEN123456abcd';
$accessToken = "EAADIZCSOHKUoBAOfBh6gzm1bLMDkkuLhl86v2RhfZCMFg1ije1ZBL82hEG1hXoJZCWSDD8VMbpEbxKyK8e6pxUpZCi5AmntoKINHYEXJDQi0yofFYMu8NxzZBKV9b8vu5W2Mk8ymelOARiTXUV4rU2BuIlZApa9WAPHxYLZAmGAPKQZDZD";
// check token at setup
if ($_REQUEST['hub_verify_token'] === $hubVerifyToken) {
	echo $_REQUEST['hub_challenge'];
	exit;
}
// handle bot's anwser
$input = json_decode(file_get_contents('php://input'), true);
$senderId = $input['entry'][0]['messaging'][0]['sender']['id'];
$messageText = $input['entry'][0]['messaging'][0]['message']['text'];
$special_command = $input['entry'][0]['messaging'][0]['postback']['payload'];
if(!empty($messageText))
{
	$menu_message = [];
	$buttons = [];
	$buttons[] = ['type' => 'postback', 'title' => 'Ask us a Question', 'payload' => 'ASK_QUESTION'];
	$buttons[] = ['type' => 'postback', 'title' => 'Use Dictionary', 'payload' => 'USE_DIC'];
	$menu_message = [
	'type'    => 'template',
	'payload' => ['template_type' => 'button', 'text' => 'Welcome to our facebook page My Easy Way To Learn English', 'buttons' => $buttons]

	];

	$response = [
		'recipient' => [ 'id' => $senderId ],
		'message' => [ 'attachment' => $menu_message ]
	];

	if (strpos($messageText, 'translate') !== false) {
	$apiKey = 'AIzaSyDwFOPkNAENeet5eTqaT6g18Eua5iQbn0I';
    	$text = $messageText;
    	$url = 'https://www.googleapis.com/language/translate/v2?key=' . $apiKey . '&q=' . rawurlencode($text) . '&source=en&target=ar';

    	$handle = curl_init($url);
    	curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    	$response = curl_exec($handle);                 
    	$responseDecoded = json_decode($response, true);
    	curl_close($handle);
		
		
	$response = [
		'recipient' => [ 'id' => $senderId ],
		'message' => [ 'text' => $responseDecoded['data']['translations'][0]['translatedText'] ]
	];
	
	}
		
}elseif ($special_command == 'ASK_QUESTION') {
		$ask_message = 'Please write your question :) ';
		

		$response = [
			'recipient' => [ 'id' => $senderId ],
			'message' => [ 'text' => $ask_message ]
		];
	}
elseif ($special_command == 'USE_DIC') {
		$ask_message = 'Type: translate <word>';
		

		$response = [
			'recipient' => [ 'id' => $senderId ],
			'message' => [ 'text' => $ask_message ]
		];
	
	
}
$ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token='.$accessToken);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
	curl_exec($ch);
	curl_close($ch);
