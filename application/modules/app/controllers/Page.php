<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Page extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->uri = site_url('app/page/');
    $this->template = 'app/page/';
    $this->page = site_url('app/page/');
    $this->load->model('m_page');
  }

  public function index($main = null ? $main = null : 'home')
  {
    // DEFAULT
    if(@$main != '') {
      $d['main'] = $main;
    } else {
      $d['main'] = 'home';
    }

    // HOME
    if(@$main == 'home') {
      $d['url'] = 'home';
      $d['perangkat_desa'] = $this->m_page->get_perangkat_desa();
      $d['jumlah_perangkat_desa'] = count(DB::all('data_perangkat_desa'));
    } 

    // ABOUT
    else if(@$main == 'about') {
      $d['url'] = 'about';
    }
    $this->render($this->template . 'index', $d);
  }
}