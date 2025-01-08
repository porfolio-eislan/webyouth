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

    // BLOG
    else if(@$main == 'blog') {
      $d['url'] = 'blog';
    }

    // Team
    else if(@$main == 'team') {
      $d['url'] = 'team';
      $d['perangkat_desa'] = $this->m_page->get_perangkat_desa_all();
      $d['jumlah_perangkat_desa'] = count(DB::all('data_perangkat_desa'));
    }

    // Visi
    else if(@$main == 'visi') {
      $d['url'] = 'visi';
    }

    // Galeri
    else if(@$main == 'galeri') {
      $d['url'] = 'galeri';
    }

    // Galeri
    else if(@$main == 'youth') {
      $d['url'] = 'youth';
    }

    // Activity
    else if(@$main == 'activity') {
      $d['url'] = 'activity';
    }

    // Venue
    else if(@$main == 'venue') {
      $d['url'] = 'venue';
    }

    if(@$main == 'home') {
      $d['title'] = 'Dadapbong - Web Dokumenter';
      $d['uri'] = $this->generateRandomString();
      $d['identitas'] = $this->m_page->get_identitas();
      $this->load->view('app/template/header', $d);
      $this->load->view('app/template/topbar', $d);
      $this->load->view('app/template/navbar', $d);
      $this->load->view('page/index', $d);
      $this->load->view('app/template/footer', $d);
    } else {
      $this->render($this->template . 'index', $d);
    }
  }
}