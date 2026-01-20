<?php
class Api
{
    public $api_url = 'https://smmpakpanel.com/api/v2';
    public $api_key = '7d74c65e6895648526249f89287548cc';

    public function order($data)
    {
        $post = array_merge(['key' => $this->api_key, 'action' => 'add'], $data);
        return json_decode((string)$this->connect($post));
    }

    public function status($order_id)
    {
        return json_decode($this->connect([
            'key' => $this->api_key,
            'action' => 'status',
            'order' => $order_id
        ]));
    }

    public function services()
    {
        return json_decode($this->connect([
            'key' => $this->api_key,
            'action' => 'services',
        ]));
    }

    public function balance()
    {
        return json_decode($this->connect([
            'key' => $this->api_key,
            'action' => 'balance',
        ]));
    }

    private function connect($post)
    {
        $_post = [];
        foreach ($post as $name => $value) {
            $_post[] = $name . '=' . urlencode($value);
        }

        $ch = curl_init($this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, join('&', $_post));
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');

        $result = curl_exec($ch);
        if (curl_errno($ch)) $result = false;
        curl_close($ch);
        return $result;
    }
}
