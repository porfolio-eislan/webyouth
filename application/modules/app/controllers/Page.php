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
    } 

    // ABOUT
    else if(@$main == 'about') {
      $d['url'] = 'about';
    }
    $this->render($this->template . 'index', $d);
  }
}