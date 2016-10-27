<?php
/* Copyright (C) Prezly https://www.prezly.com/
 * Modernize and speed up your PR workflow!
 * Written by Sam Hermans, 27/10/2016
 */

namespace MrSam\DNSimple;

class Forwarder {
    public  $api_url      = 'https://api.dnsimple.com';
    public  $api_version  = 'v2';
    public  $http_agent   = 'DNSimple-Forwarder (https://github.com/MrSam/dnsimple-forwarder)';

    private $token;


    /**
     * DNSimple_Forwarder constructor.
     * For more info read https://developer.dnsimple.com/v2/
     *
     * @param $token
     * @throws Exception
     */
    public function __construct($token)
    {
        if(strlen($token) < 5)
            throw new Exception('Invalid Authentication Token.');

        $this->token = $token;
    }

    /**
     * This function returns an array containing the accounts available using this token.
     * The array will contain the user id as key and the email as value.
     *
     * @return array
     */
    public function getAccounts() {
        $accounts = $this->http_call ('GET', '/accounts');

        $res = [];
        foreach($accounts['data'] as $account) {
            $res[$account['id']] = $account['email'];
        }

        return $res;
    }


    /**
     * This function returns an array containing the domain id as key and name as value.
     * You can get the account_id from $this->getAccounts() if you don't know it yet.
     *
     * @param $account_id
     * @return array
     * @throws Exception
     */
    public function getDomains($account_id) {

        if(!$account_id)
            throw new Exception('getDomains() Account id missing.');

        $domains = $this->http_call ('GET', "/" . $account_id . '/domains');

        $res = [];
        foreach($domains['data'] as $domain) {
            $res[$domain['id']] = $domain['unicode_name'];
        }
        return $res;
    }

    /**
     * This function returns an array containing all active forwards for a specified domain
     * Each array key contains 2 values: 'from' and 'to'
     * Get the account_id from $this->getAccounts() and the domain from $this->getDomains()
     *
     * @param $account_id
     * @param $domain
     * @return array
     * @throws Exception
     */
    public function getForwarders($account_id, $domain) {

        if(!$account_id)
            throw new Exception('getForwarders() Account id missing.');

        if(!$domain)
            throw new Exception('getForwarders() Domain missing.');

        $forwarders = $this->http_call ('GET', '/' . $account_id . '/domains/'. $domain . '/email_forwards');

        $res = [];
        foreach($forwarders['data'] as $forward) {
            $res[$forward['id']] = ['from' => $forward['from'], 'to' => $forward['to']];
        }

        return $res;
    }

    public function addForward($account_id, $domain, $from_prefix, $to)
    {
        $vars = ['from' => $from_prefix, "to" => $to];

        $res = $this->http_call ('POST', '/' . $account_id . '/domains/'. $domain . '/email_forwards', $vars);
        var_dump($res);
        return $res;
    }


    private function http_call ($method = 'GET', $path, $vars = array ())
    {
        if(!$path)
            throw new Exception('http_call() path missing.');

        $ch = curl_init();

        $url = $this->api_url . "/" . $this->api_version . $path;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, false); // enable this to debug


        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 2); // leave this on 2 to avoid man in the middle attacks
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // leave this on 2 to avoid man in the middle attacks

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        $headers = ['Authorization: Bearer ' . $this->token, 'Accept: application/json'];

        if($method == "POST" || $method == "PUT" || $method == "DELETE") {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($vars));
            $headers[] = 'Content-Length: ' . strlen(json_encode($vars));
            $headers[] = 'Content-Type: application/json';
        }

        curl_setopt($ch, CURLOPT_USERAGENT, $this->http_agent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        return json_decode(curl_exec($ch), true);
    }


}