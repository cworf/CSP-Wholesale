<?php

class Kraken
{

    protected $auth = array();
    public static $kraken_plugin_version = '2.0.0';

    public function __construct($key = '', $secret = '')
    {
        $this->auth = array(
            "auth" => array(
                "api_key" => $key,
                "api_secret" => $secret
            )
        );
    }

    public function url($opts = array())
    {
        $data = json_encode(array_merge($this->auth, $opts));
        $response = self::request($data, "https://api.kraken.io/v1/url");

        return $response;
    }

    public function upload($opts = array())
    {
        if (!isset($opts['file']))
        {
            return array(
                "success" => false,
                "error" => "File parameter was not provided"
            );
        }

        if (preg_match("/\/\//i", $opts['file']))
        {
            $opts['url'] = $opts['file'];
            unset($opts['file']);
            return $this->url($opts);
        }

        if (!file_exists($opts['file']))
        {
            return array(
                "success" => false,
                "error" => "File `" . $opts['file'] . "` does not exist"
            );
        }

        if (function_exists('curl_file_create')) {
            $file = curl_file_create($opts['file'], 'image/jpeg', $opts['file']);
        } else {
            $file = sprintf('@%s', $opts['file']);
        }

        unset($opts['file']);

        $data = array_merge(array(
            "file" => $file,
            "data" => json_encode(array_merge(
                            $this->auth, $opts
            ))
        ));

        $response = self::request($data, "https://api.kraken.io/v1/upload");

        return $response;
    }

    public function status()
    {
        $data = array('auth' => array(
            'api_key' => $this->auth['auth']['api_key'],
            'api_secret' => $this->auth['auth']['api_secret']
        ));
        $response = self::request(json_encode($data), "https://api.kraken.io/user_status");

        return $response;
    }

    private function request($data, $url)
    {
        $curl = curl_init();
        
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0); 
        curl_setopt($curl, CURLOPT_TIMEOUT, 400);        
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);      
        curl_setopt($curl, CURLOPT_USERAGENT, 'WordPress/' . get_bloginfo('version') . ' KrakenPlugin/' . self::$kraken_plugin_version);      
        curl_setopt($curl, CURLOPT_FAILONERROR, 1);

        $response = json_decode(curl_exec($curl), true);
        $error = curl_errno($curl);

        curl_close($curl);

        if ($error > 0) {
            throw new RuntimeException(sprintf('cURL returned with the following error code: "%s"', $error));
        }

        return $response;
    }

}