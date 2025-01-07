<?php
include_once './vendor/autoload.php';

use Nsulistiyawan\Bpjs\VClaim\Peserta;
use Nsulistiyawan\Bpjs\VClaim\Monitoring;
use Nsulistiyawan\Bpjs\VClaim\LembarPengajuanKlaim;
use Nsulistiyawan\Bpjs\VClaim\Referensi;
use Nsulistiyawan\Bpjs\VClaim\RencanaKontrol;
use Nsulistiyawan\Bpjs\VClaim\Rujukan;
use Nsulistiyawan\Bpjs\VClaim\Sep;
use Nsulistiyawan\Bpjs\VClaim\PRB;

class VClaim
{
  var $Config,
    $Peserta,
    $LembarPengajuanKlaim,
    $Monitoring,
    $Referensi,
    $RencanaKontrol,
    $Rujukan,
    $Sep,
    $PRB;

  public function __construct()
  {
    $this->Config = vclaim_config();
    $this->Peserta = new Peserta($this->Config);
    $this->LembarPengajuanKlaim = new LembarPengajuanKlaim($this->Config);
    $this->Monitoring = new Monitoring($this->Config);
    $this->Referensi = new Referensi($this->Config);
    $this->RencanaKontrol = new RencanaKontrol($this->Config);
    $this->Rujukan = new Rujukan($this->Config);
    $this->Sep = new Sep($this->Config);
    $this->PRB = new PRB($this->Config);
  }
}
