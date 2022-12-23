<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Google extends CI_Controller {

	public function __construct(){
		parent::__construct();

		$this->load->library('session');
	}

    public function index(){

    }

    public function configureClient(){
        $client = new Google\Client();
        try {
            $client->setAuthConfig("cdn/jadiorder.json");
            $client->addScope(Google_Service_FirebaseCloudMessaging::CLOUD_PLATFORM);

            // retrieve the saved oauth token if it exists, you can save it on your database or in a secure place on your server
            $savedTokenJson = $this->func->globalset("google_token");

            if ($savedTokenJson != "") {
                // the token exists, set it to the client and check if it's still valid
                $client->setAccessToken($savedTokenJson);
                if ($client->isAccessTokenExpired()) {
                    // the token is expired, generate a new token and set it to the client
                    $accessToken = $this->generateToken($client);
                    $client->setAccessToken($accessToken);
                }
            } else {
                // the token doesn't exist, generate a new token and set it to the client
                $accessToken = $this->generateToken($client);
                $client->setAccessToken($accessToken);
            }

            $oauthToken = $accessToken["access_token"];
            $this->db->where("field","google_token");
            $this->db->update("setting",["value"=>$accessToken["access_token"]]);

            // the client is configured, now you can send the push notification using the $oauthToken.

            echo $oauthToken;

        } catch (Google_Exception $e) {
            // handle exception
            echo "Error";
        }
    }

    private function generateToken($client){
        $client->fetchAccessTokenWithAssertion();
        $accessToken = $client->getAccessToken();

        // save the oauth token json on your database or in a secure place on your server
        //$tokenJson = json_encode($accessToken);
        //$this->saveFile($tokenJson);
        $this->db->where("field","google_token");
        $this->db->update("setting",["value"=>json_encode($accessToken)]);

        return $accessToken;
    }

    public function sendFCM(){
        $token = "dCA27DrGTW-dXZmnW87OW8:APA91bFN8b705AYys--0eOQeB04jBxt5zvzRgq63q9RItAKrSd66z-DvOKw_yrDzTanTSA9mQDmhEc4i6B2TaiCgC-dgFNtySNAoHY2XGm417hY1_m9iej7Jlz1YHenFYvrnrMTG3hnt"; //Token generated in app
        $title = "FCM Message!!";
        $body = "Tes ke device tertentu";

        $client = new Google_Client();

        // Set auth config file
        $client->setAuthConfig("cdn/token/jadiorder.json");
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');//Google_Service_FirebaseCloudMessaging::CLOUD_PLATFORM
        // Returns an instance of GuzzleHttp\Client that authenticates with the Google API.
        $httpClient = $client->authorize();

        // Your Firebase project ID
        $project = 'jadiorder-mobile';

        // Creates a notification for subscribers to the debug topic
        $message = [
            "message" => [
                // Send with token is not working, but works without it
                "token" => $token,
                //"topic" => "all",
                "notification" => [
                    "body" => $body,
                    "title" => $title,
                ]
            ]
        ];

        // Send the Push Notification - use $response to inspect success or errors
        $response = $httpClient->post("https://fcm.googleapis.com/v1/projects/$project/messages:send", ['json' => $message]);
        print_r($response->getStatusCode());
    }

    function cekIP(){
        $url = 'http://google.com';
        $wrapper = fopen('php://temp', 'r+');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_STDERR, $wrapper);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $ips = $this->get_curl_remote_ips($wrapper);
        fclose($wrapper);

        var_dump($ips);  // 208.69.36.231

    }
    function get_curl_remote_ips($fp){
        rewind($fp);
        $str = fread($fp, 8192);
        $regex = '/\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b/';
        if (preg_match_all($regex, $str, $matches)) {
            return array_unique($matches[0]);  // Array([0] => 74.125.45.100 [2] => 208.69.36.231)
        } else {
            return false;
        }
    }
}