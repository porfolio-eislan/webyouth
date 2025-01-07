<?php
class BasicRest
{

    private $config;
    private $error;
    private $header;

    function __construct()
    {
        $this->config = include('config.php');
    }

    public function getError()
    {
        return $this->error;
    }

    public function getHeader()
    {
        return $this->header;
    }

    public function send($url, $method, $queryData = null, array $files = null)
    {
        date_default_timezone_set("Asia/Jakarta");
        $curl = curl_init();
        $headers = [];

        if (!is_null($queryData)) $content = $queryData;
        else $content = [];

        $auth = base64_encode($this->config['client_id'] . ':' . $this->config['client_secret']);

        if (!is_null($files)) {
            $header = 'Content-Type: application/json;';
            $form_data = array_merge($files, $content);
        } else {
            $header = 'Content-Type: application/json;';
            $form_data = $content;
        }
        date_default_timezone_set("Asia/Jakarta");
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->config['host'] . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADERFUNCTION => function ($curl, $head) use (&$headers) {
                $len = strlen($head);
                $head = explode(':', $head, 2);
                if (count($head) < 2) // ignore invalid headers
                    return $len;

                $headers[strtolower(trim($head[0]))][] = trim($head[1]);

                return $len;
            },
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POSTFIELDS => json_encode($form_data),
            CURLOPT_HTTPHEADER => array(
                'cache-control: no-cache',
                'Authorization: Basic ' . $auth,
                $header
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            $res = json_decode($response, true);
            if ($res) {
                return $res;
            } else {
                $this->error = $err;
                // return false;
                $res_error = array(
                    'status_code' => 522,
                    'error' => @$err
                );
                return $res_error;
            }
        } else {
            $this->header = $headers;
            $res = json_decode($response, true);
            if (json_last_error() == JSON_ERROR_NONE) {
                if (isset($res->error)) {
                    $this->error = $res->error;
                    return false;
                } else return $res;
            } else return $response;
        }
    }
}
