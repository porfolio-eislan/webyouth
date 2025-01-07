<?php
defined('BASEPATH') or exit('No direct script access allowed');

use GuzzleHttp\Client;

class Lis
{
  var $access_token;
  var $ci;
  var $config;

  function __construct()
  {
    $this->ci = get_instance();
    $this->ci->load->database();
    $this->get_config();
  }

  private function get_config()
  {
    $this->config = $this->ci->db->get("lis_config")->row_array();
  }

  public function connection()
  {
    // $consid = $this->config['xcons'];
    // $secret = $this->config['xkey'];

    // date_default_timezone_set('UTC');
    // $x_time = strval(time() - strtotime('1970-01-01 00:00:00'));
    // $signature = hash_hmac('sha256', $consid, $secret, true);
    // $x_sign = base64_encode($signature);

    $headers = array(
      "Content-Type:application/json"
      // "X-cons:" . $consid,
      // "X-time: " . $x_time,
      // "X-sign: " . $x_sign
    );

    $server = $this->config['base_url'];

    return array(
      'header' => $headers,
      'server' => $server,
    );
  }

  public function get_result_pdf($no_transaksi)
  {
    $connection = $this->connection();

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $connection['server'] . '/api_lis/cetak_hasil.php/order/' . @$no_transaksi,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => $connection['header'],
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    if (isset(json_decode(@$response, true)['response']['data']) == 1) {
      $result = array('header' => 'Ops!', 'status' => 'error', 'msg' => json_decode(@$response, true)['metaData']['message'], 'data' => '');
    } else {
      # Write the PDF contents to a local file
      $file_path = FCPATH . 'assets/pdf/hasil_pemeriksaan_lab/' . $no_transaksi . '.pdf';
      file_put_contents($file_path, $response);

      header('Content-type: application/pdf');

      $result = array('header' => 'Yeay!', 'status' => 'success', 'msg' => 'Data berhasil diambil', 'data' => @$no_transaksi);
    }
    // return readfile($file_path);
    return @$result;
  }

  public function get_result_json($no_transaksi)
  {
    $connection = $this->connection();

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $connection['server'] . '/api_lis/view.php/order/' . @$no_transaksi,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => $connection['header'],
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    // $response = json_decode($response, true);

    return $response;
  }

  public function order_laboratorium($data = [])
  {
    $connection = $this->connection();

    $uri = $connection['server'] . "/api_lis/index.php";
    $ch = curl_init($uri);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt(
      $ch,
      CURLOPT_HTTPHEADER,
      $connection['header']
    );
    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode($result);
  }

  // public function update_laboratorium($data = [])
  // {
  //   $connection = $this->connection();

  //   $uri = $connection['server'] . "/api/v2/saveAdditional/order";
  //   $ch = curl_init($uri);
  //   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  //   curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  //   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  //   curl_setopt(
  //     $ch,
  //     CURLOPT_HTTPHEADER,
  //     $connection['header']
  //   );
  //   $result = curl_exec($ch);
  //   curl_close($ch);

  //   return json_decode($result);
  // }
}
