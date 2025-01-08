<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->uri = site_url('app/admin/dashboard/');
    $this->template = 'app/admin/dashboard/';
    $this->dashboard = site_url('app/admin/dashboard/');
    $this->load->model('admin/m_dashboard');
    $this->load->model('m_page');
  }

  public function index($main = null)
  {
    $d['main'] = '';
    $this->render_admin($this->template . 'index', $d);
  }
}