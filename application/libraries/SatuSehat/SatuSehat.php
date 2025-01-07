<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SatuSehat
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
    $this->config = satusehat_config();
  }

  private function update_config($data)
  {
    $this->ci->db->update('conf_bridging', $data);
  }

  public function generate_token_new()
  {
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->config['auth_url'] . '/accesstoken?grant_type=client_credentials',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => 'client_id=' . $this->config['client_id'] . '&client_secret=' . $this->config['client_secret'],
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded'
      ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $response = json_decode($response, true);

    if (@$response['access_token'] != '') {
      $this->update_config(array(
        'satusehat_access_token' => @$response['access_token'],
        'satusehat_expired_token' => date('Y-m-d H:i:s', strtotime('+' . @$response['expires_in'] . ' seconds', strtotime(date('Y-m-d H:i:s'))))
      ));
    }

    return $response;
  }

  public function generate_token()
  {
    $curDate = strtotime(date('Y-m-d H:i:s'));
    $expiredDate = strtotime($this->config['expired_token']);

    if ($curDate < $expiredDate) {
      // token available
      $response = null;
    } else {
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => $this->config['auth_url'] . '/accesstoken?grant_type=client_credentials',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'client_id=' . $this->config['client_id'] . '&client_secret=' . $this->config['client_secret'],
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/x-www-form-urlencoded'
        ),
      ));
      $response = curl_exec($curl);
      curl_close($curl);
      $response = json_decode($response, true);

      if (@$response['access_token'] != '') {
        $this->update_config(array(
          'satusehat_access_token' => @$response['access_token'],
          'satusehat_expired_token' => date('Y-m-d H:i:s', strtotime('+' . @$response['expires_in'] . ' seconds', strtotime(date('Y-m-d H:i:s'))))
        ));
      }
    }

    return $response;
  }

  public function authenticateWithOAuth2()
  {
    $curl = curl_init();
    $params = [
      'grant_type' => 'client_credentials',
      'client_id' => $this->config['client_id'],
      'client_secret' => $this->config['client_secret']
    ];

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->config['auth_url'] . "/accesstoken?grant_type=client_credentials",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => http_build_query($params),
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    //echo $response;
    // Parse the response body
    $data = json_decode($response, true);

    // Return the access token
    return $data['access_token'];
  }

  public function get_encounter_by_subject($pasien_id)
  {
    $this->generate_token();
    $curl = curl_init();

    $pasien = $this->ci->db->where('pasien_id', $pasien_id)->get("mst_pasien")->row_array();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->config['base_url'] . '/Encounter?subject=' . @$pasien['ihs_id'],
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $this->config['access_token']
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response, true);

    return $response;
  }

  public function get_encounter_by_id($id)
  {
    $this->generate_token();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->config['base_url'] . '/Encounter/' . $id,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $this->config['access_token']
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response, true);

    return $response;
  }

  public function get_condition_by_encounter_id($id)
  {
    $this->generate_token();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->config['base_url'] . '/Condition?encounter=' . $id,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $this->config['access_token']
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response, true);

    return $response;
  }

  public function get_organization_by_id($id)
  {
    $this->generate_token();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->config['base_url'] . '/Organization/' . $id,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $this->config['access_token']
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response, true);

    return $response;
  }

  // Location
  public function post_location($payload)
  {
    $this->generate_token();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->config['base_url'] . '/Location',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => json_encode($payload),
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $this->config['access_token'],
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response, true);

    return $response;
  }

  public function put_location($payload, $id)
  {
    $this->generate_token();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->config['base_url'] . '/Location/' . $id,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'PUT',
      CURLOPT_POSTFIELDS => json_encode($payload),
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $this->config['access_token'],
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response, true);

    return $response;
  }
  // END Location

  // Organization
  public function post_organization($payload)
  {
    $this->generate_token();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->config['base_url'] . '/Organization',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => json_encode($payload),
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $this->config['access_token'],
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response, true);

    return $response;
  }

  public function put_organization($payload, $id)
  {
    $this->generate_token();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->config['base_url'] . '/Organization/' . $id,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'PUT',
      CURLOPT_POSTFIELDS => json_encode($payload),
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $this->config['access_token'],
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response, true);

    return $response;
  }
  // END Organization

  // Practitioner
  public function get_practitioner_by_nik($nik)
  {
    $this->generate_token();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->config['base_url'] . "/Practitioner?identifier=https://fhir.kemkes.go.id/id/nik|$nik",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $this->config['access_token']
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response, true);

    return $response;
  }
  // END Practitioner

  // Patient
  public function get_patient_by_nik($nik)
  {
    $this->generate_token();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->config['base_url'] . "/Patient?identifier=https://fhir.kemkes.go.id/id/nik|$nik",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $this->config['access_token']
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response, true);

    return $response;
  }
  // END Patient

  // Encounter
  public function post_encounter($payload)
  {
    $this->generate_token();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->config['base_url'] . '/Encounter',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => json_encode($payload),
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $this->config['access_token'],
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response, true);

    return $response;
  }

  public function put_encounter($payload, $id)
  {
    $this->generate_token();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->config['base_url'] . '/Encounter/' . $id,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'PUT',
      CURLOPT_POSTFIELDS => json_encode($payload),
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $this->config['access_token'],
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response, true);

    return $response;
  }
  // END Encounter

  // Condition
  public function post_condition($payload)
  {
    $this->generate_token();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->config['base_url'] . '/Condition',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => json_encode($payload),
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $this->config['access_token'],
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response, true);

    return $response;
  }

  public function put_condition($payload, $id)
  {
    $this->generate_token();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->config['base_url'] . '/Condition/' . $id,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'PUT',
      CURLOPT_POSTFIELDS => json_encode($payload),
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $this->config['access_token'],
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response, true);

    return $response;
  }
  // END Condition

  // Observation
  public function post_observation($payload)
  {
    $this->generate_token();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->config['base_url'] . '/Observation',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => json_encode($payload),
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $this->config['access_token'],
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response, true);

    return $response;
  }

  public function put_observation($payload, $id)
  {
    $this->generate_token();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->config['base_url'] . '/Observation/' . $id,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'PUT',
      CURLOPT_POSTFIELDS => json_encode($payload),
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $this->config['access_token'],
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response, true);

    return $response;
  }
  // END Observation

  // Procedure
  public function post_procedure($payload)
  {
    $this->generate_token();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->config['base_url'] . '/Procedure',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => json_encode($payload),
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $this->config['access_token'],
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response, true);

    return $response;
  }

  public function put_procedure($payload, $id)
  {
    $this->generate_token();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->config['base_url'] . '/Procedure/' . $id,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'PUT',
      CURLOPT_POSTFIELDS => json_encode($payload),
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $this->config['access_token'],
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response, true);

    return $response;
  }
  // END Procedure

  // KFA
  public function get_product_detail($identifier = '', $code = '')
  {
    $this->generate_token();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->config['base_url_kfa'] . '/kfa-v2/products?identifier=' . @$identifier . '&code=' . @$code,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $this->config['access_token']
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response, true);

    return $response;
  }
  // END KFA
}
