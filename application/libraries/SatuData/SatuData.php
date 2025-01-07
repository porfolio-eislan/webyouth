<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SatuData
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
    $this->config = $this->ci->db->get("satudata_config")->row_array();
  }

  public function connection()
  {
    $consid = $this->config['const_id'];
    $secret = $this->config['secret_id'];
    $user_id = $this->config['faskes_id'];

    $header = json_encode(['user' => $consid, 'pass' => $secret]);
    $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
    $payload = json_encode(['user_id' => $user_id, 'tStamp' => $tStamp]);
    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'ab123', true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    $token = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;


    $headers = array(
      "Content-Type:application/json",
      "x-username:" . $consid,
      "x-password: " . $secret,
      "x-token: " . $token
    );

    $server = $this->config['base_url'];

    return array(
      'consid' => $consid,
      'secret' => $secret,
      'token' => $token,
      'server' => $server,
    );
  }

  public function send_kunjungan($pasien_id, $reg_id)
  {
    $connection = $this->connection();
    $reg = $this->ci->db->where('pasien_id', $pasien_id)->where('reg_id', $reg_id)->get("reg_pasien")->row_array();
    $diagnosis = $this->ci->db->where('pasien_id', $pasien_id)->where('reg_id', $reg_id)->group_by('icdx')->get("dat_diagnosis")->result_array();

    if (@$reg['statuspasien_cd'] == 'B') {
      $kunjungan = 'BARU';
    } elseif (@$reg['statuspasien_cd'] == 'L') {
      $kunjungan = 'LAMA';
    }

    foreach ($diagnosis as $key => $diag) {
      if (@$diag['jenisdiagnosis_cd'] == 'B') {
        $kasus = 'BARU';
      } elseif (@$diag['jenisdiagnosis_cd'] == 'K') {
        $kasus = 'KUNJUNGAN KASUS';
      } elseif (@$diag['jenisdiagnosis_cd'] == 'L') {
        $kasus = 'LAMA';
      }

      $data = array(
        "tanggal" => to_date(@$reg['tgl_registrasi'], '', 'date'),
        "nik" => @$reg['nik'],
        "nama" => @$reg['pasien_nm'],
        "alamat" => @$reg['alamat'],
        "desa" => @$reg['kelurahan'],
        "kecamatan" => @$reg['kecamatan'],
        "kabupaten" => @$reg['kabupaten'],
        "propinsi" => @$reg['provinsi'],
        "icdx" => $diag['icdx'],
        "kunjungan" => $kunjungan,
        "kasus" => $kasus
      );
      // echo '<pre>' . var_export($data, true) . '</pre>';
      // exit;

      $data_string = json_encode($data);
      $uri = $connection['server'] . "kirim";
      $ch = curl_init($uri);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
          'Content-Type: application/json',
          'Content-Length: ' . strlen($data_string),
          "x-username:" . $connection['consid'],
          "x-password: " . $connection['secret'],
          "x-token: " . $connection['token']

        )
      );
      $result = curl_exec($ch);
      curl_close($ch);
    }

    return json_decode($result);
  }
}
