<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends MX_Controller
{
  
  public function __construct()
  {
    parent::__construct();
  }

  public function generateRandomString($length = 60) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }

  public function render($content, $data = array())
  {
    $data['title'] = 'Dadapbong - Web Dokumenter';
    $data['uri'] = $this->generateRandomString();
    $data['identitas'] = $this->m_page->get_identitas();
    $this->load->view('app/template/header', $data);
    $this->load->view('app/template/topbar', $data);
    $this->load->view('app/template/navbar', $data);
    $this->load->view('app/ext/breadcrumb', $data);
    $this->load->view($content, $data);
    $this->load->view('app/template/footer');
  }

  public function render_admin($content, $data = array())
  {
    $data['title'] = 'Dadapbong - Web Dokumenter';
    $data['uri'] = $this->generateRandomString();
    $data['identitas'] = $this->m_page->get_identitas();
    $this->load->view('app/template/header-admin', $data);
    $this->load->view('app/template/sidebar-admin', $data);
    $this->load->view($content, $data);
    $this->load->view('app/template/footer-admin');
  }
}
