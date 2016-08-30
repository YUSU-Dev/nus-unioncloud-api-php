<?php

namespace UnionCloud;

use \Curl\Curl as Curl;
use \Exception as Exception;

class Api {
    
    private $VERSION = "0.1.0";
    
    private $host;
    
    private $auth_token;
    private $auth_token_expires;
    
    public function setHost($domain) {
        $this->host = $domain;
    }
    
    public function setAuthToken($token, $token_expires) {
        $this->auth_token = $token;
        $this->auth_token_expires = time() + $token_expires;
    }

    
    
    
    #
    # Curl functions
    #
    private function _curl($endpoint, $verb) {
        $curl = new Curl();
        $curl->setUserAgent('UnionCloud API PHP Wrapper v' . $this->VERSION);
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, true);
        $curl->setOpt(CURLOPT_SSL_VERIFYHOST, 2);
        
        $curl->setHeader("Content-Type", "application/json");
        if ($this->auth_token != null) {
            $curl->setHeader("auth_token", $this->auth_token);
        }
        $curl->setHeader("accept-version", "v1");
        
        $curl->setDefaultJsonDecoder(true);
        
        $curl->setURL("https://". $this->host . "/api" . $endpoint);
        
        if ($verb == "POST") {
            $curl->setOpt(CURLOPT_POST, true);
        } else if ($verb != "GET") {
            $curl->setOpt(CURLOPT_CUSTOMREQUEST, $verb);
        }
        
        return $curl;
    }
    
    private function _curl_debug($curl, $echo_pre = false) {
        $debug_data = [
            "url" => $curl->getOpt(CURLOPT_URL),
            "request_headers" => iterator_to_array($curl->requestHeaders),
            "request" => @$curl->getOpt(CURLOPT_POSTFIELDS),
            "response_headers" => iterator_to_array($curl->responseHeaders),
            "response" => $curl->response
        ];
        
        if ($echo_pre) {
            return "<pre>" . print_r($debug_data, true) . "</pre>";
        } else {
            return $debug_data;
        }
    }
    
    private function _curl_exceptions($where) {
        if (array_key_exists("errors", $where)) {
            throw new Exception($where["errors"][0]["error_message"], str_replace("ERR", "", $where["errors"][0]["error_code"]));
        }
    }
    
    private function _get($endpoint, $get_fields = null) {
        $api_endpoint = $endpoint;
        if ($get_fields != null) {
            $api_endpoint .= "?" . http_build_query($get_fields);
        }
        $curl = $this->_curl($api_endpoint, "GET");
        $curl->exec();
        
        return $curl;
    }
    
    private function _post($endpoint, $post_data, $get_fields = null) {
        $api_endpoint = $endpoint;
        if ($get_fields != null) {
            $api_endpoint .= "?" . http_build_query($get_fields);
        }
        $curl = $this->_curl($api_endpoint, "POST");
        $curl->setOpt(CURLOPT_POSTFIELDS, json_encode($post_data, JSON_UNESCAPED_SLASHES));
        $curl->exec();
        
        //if (array_key_exists("error", $curl->response)) {
        //    throw new Exception($curl->response["error"]["error_message"], $curl->response["error"]["error_code"]);
        //}
        
        return $curl;
    }
    
    private function _put($endpoint, $data) {
        $curl = $this->_curl($endpoint, "PUT");
        $curl->setOpt(CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_SLASHES));
        $curl->exec();
        return $curl;
    }
    
    private function _delete($endpoint, $data = null) {
        $curl = $this->_curl($endpoint, "DELETE");
        $curl->exec();     
        return $curl;
    }
    
    
    
    #
    # Authenticate
    #
    public function authenticate($email, $password, $app_id, $app_secret) {
        $data = [
            "email" => $email,
            "password" => $password,
            "app_id" => $app_id,
            "date_stamp" => strval(time()),
            "hash" => hash("sha256", $email . $password . $app_id . strval(time()) . $app_secret),
        ];
        
        $curl = $this->_post("/authenticate", $data);
        
        if ($curl->response["result"] == "SUCCESS") {
            $this->setAuthToken($curl->response["response"]["auth_token"], $curl->response["response"]["expires"]);
        } else {
            throw new Exception($curl->response["error"]["message"], $curl->response["error"]["code"]);
        }
    }

    
    
    
    #
    # Uploads
    #
    public function upload_student($data) {
        $curl = $this->_post("/json/upload/students", ["data" => $data]);
        return @$curl->response["data"];
    }
    
    public function upload_guest($data) {
        $curl = $this->_post("/json/upload/guests", ["data" => $data]);
        return @$curl->response["data"];
    }
    
    public function upload_programme($data) {
        $curl = $this->_post("/json/upload/programmes", ["data" => $data]);
        return @$curl->response["data"];
    }

    
    
    
    #
    # Users
    #
    public function user_search($filters, $mode = "standard") {
        $curl = $this->_post("/users/search", ["data" => $filters], ["mode" => $mode, "page" => 1]);
        return @$curl->response["data"];
    }
    
    public function user_get($uid, $mode = "standard") {
        $curl = $this->_get("/users/".$uid, ["mode" => $mode]); 
        return @$curl->response["data"][0];
    }
    
    public function user_get_group_memberships($uid, $mode = "standard") {
        $curl = $this->_get("/users/".$uid."/user_group_memberships", ["mode" => $mode]);
        return @$curl->response["data"];
    }
    
    public function user_update($uid, $data) {
        $curl = $this->_put("/users/".$uid, ["data" => $data]);
        return $curl->response["data"][0]; 
    }
    
    public function user_delete($uid) {
        $curl = $this->_delete("/users/".$uid);
        return $curl->response["data"][0];
    }

    
    
    
    #
    # UserGroups
    #
    public function usergroup_all($mode = "standard") {
        $curl = $this->_get("/user_groups", ["mode" => $mode]); 
        return $curl->response["data"];
    }
    
    public function usergroup_search($filters, $mode = "standard") {
        $curl = $this->_post("/user_groups/search", ["data" => $filters], ["mode" => $mode]); 
        return $curl->response["data"];
    }
    
    public function usergroup_create($data) {
        $curl = $this->_post("/user_groups", ["data" => $data]); 
        return $curl->response["data"];
    }
    
    public function usergroup_get($ug_id, $mode = "standard") {
        $curl = $this->_get("/user_groups/".$ug_id, ["mode" => $mode]); 
        return $curl->response["data"][0];
    }
    
    public function usergroup_get_members($ug_id, $mode = "standard", $from = null, $to = null) {
        $curl = $this->_get("/user_groups/".$ug_id."/user_group_memberships", ["mode" => $mode]); 
        return $curl->response["data"];
    }
    
    public function usergroup_update($ug_id, $data) {
        $curl = $this->_put("/user_groups/".$ug_id, ["data" => $data]); 
        return $curl->response["data"][0];
    }
    
    public function usergroup_delete($ug_id) {
        $curl = $this->_delete("/user_groups/".$ug_id); 
        return $curl->response["data"][0];
    }

    public function usergroup_folderstructure() {
        $curl = $this->_get("/user_groups/folderstructure"); 
        return $curl->response["data"];
    }
    
    
    #
    # UserGroup Membership
    #
    public function usergroup_membership_create($data) {
        $curl = $this->_post("/user_group_memberships", ["data" => $data]); 
        return $curl->response["data"];
    }
    
    public function usergroup_membership_update($ugm_id, $data) {
        $curl = $this->_put("/user_group_memberships/".$ugm_id, ["data" => $data]); 
        return $curl->response["data"];
    }
    
    public function usergroup_membership_delete($ugm_id) {
        $curl = $this->_delete("/user_group_memberships/".$ugm_id, ["data" => $data]); 
        return $curl->response["data"];
    }

    
    
    
    #
    # Event Types
    #
    public function eventtypes_get() {
        $curl = $this->_get("/event_types");
        return $curl->response["data"];
    }

    
    
    
    #
    # Events
    #
    public function event_all($mode = "standard") {
        $curl = $this->_get("/events", ["mode" => $mode]); 
        return $curl->response["data"];
    }
    
    public function event_search($filters, $mode = "standard") {
        $curl = $this->_post("/events/search", ["data" => $filters], ["mode" => $mode]);
        return $curl->response["data"];
    }
    
    public function event_create($data) {
        $curl = $this->_post("/events", ["data" => $data]); 
        return $curl->response["data"];
    }
    
    public function event_get($event_id, $mode = "standard") {
        $curl = $this->_get("/events/".$event_id, ["mode" => $mode]);
        return $curl->response["data"][0];
    }
    
    public function event_update($event_id, $data) {
        $curl = $this->_put("/events/".$event_id, ["data" => $data]); 
        return $curl->response["data"];
    }
    
    public function event_cancel($event_id) {
        $curl = $this->_put("/events/".$event_id."/cancel"); 
        return $curl->response["data"];
    }
    
    public function event_attendees($event_id, $mode = "standard") {
        $curl = $this->_get("/events/".$event_id."/attendees", ["mode" => $mode]);
        return $curl->response["data"];
    }

    
    
    
    #
    # Event Ticket Types
    #
    public function event_tickettype_create($event_id, $data) {
        $curl = $this->_post("/events/".$event_id."/event_ticket_types", ["data" => $data]); 
        return $curl->response["data"];
    }
    
    public function event_tickettype_update($event_id, $event_ticket_type_id, $data) {
        $curl = $this->_put("/events/".$event_id."/event_ticket_types/".$event_ticket_type_id, ["data" => $data]); 
        return $curl->response["data"];
    }
    
    public function event_tickettype_delete($event_id, $event_ticket_type_id) {
        $curl = $this->_delete("/events/".$event_id."/event_ticket_types/".$event_ticket_type_id); 
        return $curl->response["data"];
    }

    
    
    
    #
    # Event Questions
    #
    public function event_question_create($event_id, $data) {
        $curl = $this->_post("/events/".$event_id."/questions", ["data" => $data]); 
        return $curl->response["data"];
    }
    
    public function event_question_update($event_id, $question_id, $data) {
        $curl = $this->_put("/events/".$event_id."/questions/".$question_id, ["data" => $data]); 
        return $curl->response["data"];
    }
    
    public function event_question_delete($event_id, $question_id) {
        $curl = $this->_delete("/events/".$event_id."/questions/".$question_id); 
        return $curl->response["data"];
    }
    
}