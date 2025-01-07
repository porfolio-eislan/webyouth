<?php
defined('BASEPATH') or exit('No direct script access allowed');

class WhatsApp
{
  var $access_token;
  var $ci;
  var $config;

  function __construct()
  {
    $this->ci = get_instance();
    $this->ci->load->database();
    $this->get_config();

    // @encrypt
    $this->ci->load->library('encryption');
    $this->ci->encryption->initialize(
      array(
        'cipher' => 'aes-256',
        'mode' => 'ctr',
        'key' => $this->ci->config->item('encryption_key'),
      )
    );
  }

  private function get_config()
  {
    $this->config = wa_config();
  }

  private function update_config($data)
  {
    $this->ci->db->update('conf_bridging', $data);
  }

  // Broadcast
  public function get_list_whatsapp_broadcast()
  {
    $curl = curl_init();

    curl_setopt_array($curl, [
      CURLOPT_URL => $this->config['url'] . $this->config['postfix'] . "/v1/broadcasts/whatsapp",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . $this->config['access_token']
      ],
    ]);

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response, true);

    return $response;
  }
  // END Broadcast
}
