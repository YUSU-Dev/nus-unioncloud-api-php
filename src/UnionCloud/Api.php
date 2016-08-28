<?php

namespace UnionCloud;

use \Curl\Curl as Curl;
use \Exception as Exception;

class Api {
    
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
    public function _curl($endpoint, $verb) {
        $curl = new Curl();
        $curl->setUserAgent('UnionCloud API PHP Wrapper v-dev-master');
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        
        $curl->setHeader("Content-Type", "application/json");
        if ($this->auth_token != null) {
            $curl->setHeader("auth_token", $this->auth_token);
        }
        $curl->setHeader("accept-version", "v1");
        
        $curl->setDefaultJsonDecoder(true);
        
        $curl->setURL("https://". $this->host . "/api" . $endpoint);
        //$curl->setOpt(CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_SLASHES));
        
        if ($verb == "POST") {
            $curl->setOpt(CURLOPT_POST, true);
        } else if ($verb != "GET") {
            $curl->setOpt(CURLOPT_CUSTOMREQUEST, $verb);
        }
        
        return $curl;
    }
    
    public function _curl_debug($curl) {
        return [
            "url" => $curl->getOpt(CURLOPT_URL),
            "request_headers" => iterator_to_array($curl->requestHeaders),
            "request" => @$curl->getOpt(CURLOPT_POSTFIELDS),
            "response_headers" => iterator_to_array($curl->responseHeaders),
            "response" => $curl->response
        ];
    }
    
    public function _curl_exceptions($where) {
        if (array_key_exists("errors", $where)) {
            throw new Exception($where["errors"][0]["error_message"], str_replace("ERR", "", $where["errors"][0]["error_code"]));
        }
    }
    
    public function _get($endpoint, $get_fields = null) {
        $api_endpoint = $endpoint;
        if ($get_fields != null) {
            $api_endpoint .= "?" . http_build_query($get_fields);
        }
        $curl = $this->_curl($api_endpoint, "GET");
        $curl->exec();
        
        //echo "<pre>". print_r($this->_curl_debug($curl), true) ."</pre>";
        return $curl;
    }
    
    public function _post($endpoint, $post_data, $get_fields = null) {
        $api_endpoint = $endpoint;
        if ($get_fields != null) {
            $api_endpoint .= "?" . http_build_query($get_fields);
        }
        $curl = $this->_curl($api_endpoint, "POST");
        $curl->setOpt(CURLOPT_POSTFIELDS, json_encode($post_data, JSON_UNESCAPED_SLASHES));
        $curl->exec();
        
        //echo "<pre>". print_r($this->_curl_debug($curl), true) ."</pre>";
        return $curl;
    }
    
    public function _put($endpoint, $data) {
        $curl = $this->_curl($endpoint, "PUT");
        $curl->setOpt(CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_SLASHES));
        $curl->exec();
        return $curl;
    }
    
    public function _delete($endpoint, $data = null) {
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
    public function upload_student() {
        throw new Exception("Not implemented");
    }
    
    public function upload_guest() {
        throw new Exception("Not implemented");
    }
    
    public function upload_programme() {
        throw new Exception("Not implemented");
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
    # UserGrops
    #
    public function usergroup_search($filters, $mode = "standard") {
        throw new Exception("Not implemented");
    }
    
    public function usergroup_create($data) {
        throw new Exception("Not implemented");
    }
    
    public function usergroup_get() {
        throw new Exception("Not implemented");
    }
    
    public function usergroup_update($ug_id, $data) {
        throw new Exception("Not implemented");
    }
    
    public function usergroup_delete($ug_id) {
        throw new Exception("Not implemented");
    }

    
    
    
    #
    # Event Questions
    #
    public function usergroup_memberships_create($data) {
        throw new Exception("Not implemented");
    }
    
    public function usergroup_memberships_update($ugm_id, $data) {
        throw new Exception("Not implemented");
    }
    
    public function usergroup_memberships_delete($ugm_id) {
        throw new Exception("Not implemented");
    }

    
    
    
    #
    # Event Types
    #
    public function eventtypes_get() {
        throw new Exception("Not implemented");
    }

    
    
    
    #
    # Events
    #
    public function event_search($filters, $mode = "standard") {
        throw new Exception("Not implemented");
    }
    
    public function event_create($data) {
        throw new Exception("Not implemented");
    }
    
    public function event_get($event_id, $mode = "standard") {
        throw new Exception("Not implemented");
    }
    
    public function event_update($event_id, $data) {
        throw new Exception("Not implemented");
    }
    
    public function event_cancel($event_id) {
        throw new Exception("Not implemented");
    }
    
    public function event_attendees($event_id, $mode = "standard") {
        throw new Exception("Not implemented");
    }

    
    
    
    #
    # Event Ticket Types
    #
    public function event_tickettype_create($event_id, $data) {
        throw new Exception("Not implemented");
    }
    
    public function event_tickettype_update($event_id, $event_ticket_type_id, $data) {
        throw new Exception("Not implemented");
    }
    
    public function event_tickettype_delete($event_id, $event_ticket_type_id) {
        throw new Exception("Not implemented");
    }

    
    
    
    #
    # Event Questions
    #
    public function event_question_create($event_id, $data) {
        throw new Exception("Not implemented");
    }
    
    public function event_question_update($event_id, $question_id, $data) {
        throw new Exception("Not implemented");
    }
    
    public function event_question_delete($event_id, $question_id) {
        throw new Exception("Not implemented");
    }
    
}