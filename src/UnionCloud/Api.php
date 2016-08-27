<?php

namespace UnionCloud;

class Api {
    
    private $host;
    private $auth_token;
    private $auth_token_expires;
    
    public function setHost($domain) {
        $this->host = $domain;
    }
    
    public function setAuthToken($token, $token_expires) {
        $this->auth_token = $token;
        $this->auth_token_expires = $token_expires;
    }

    
    
    
    #
    # Authenticate
    #
    public function authenticate($email, $password, $app_id, $app_secret) {
        throw new Exception("Not implemented");
    }

    
    
    
    #
    # Event Questions
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
        throw new Exception("Not implemented");
    }
    
    public function user_get($uid, $mode = "standard") {
        throw new Exception("Not implemented");
    }
    
    public function user_get_group_memberships($uid, $mode = "standard") {
        throw new Exception("Not implemented");
    }
    
    public function user_update($uid, $data) {
        throw new Exception("Not implemented");
    }
    
    public function user_delete($uid) {
        throw new Exception("Not implemented");
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