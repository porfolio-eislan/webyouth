<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Notifikasi
{

  private static function tokenGenerate()
  {
    $_POST['_token'] = md5('notifikasi');
  }

  public static function kirim()
  {
    Notifikasi::tokenGenerate();
    DB::insert('app_notifikasi', [
      'notifikasi_id' => DB::get_id('app_notifikasi'),
      'notifikasi_nm' => "INI ADALAH NOTIFIKASI",
    ]);
  }
}
