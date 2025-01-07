<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// require_once("./vendor/dompdf/dompdf/autoload.inc.php");
use Dompdf\Dompdf;
use Dompdf\Options;

class Dpdf extends DOMPDF{

  protected function ci()
  {
    return get_instance();
  }

  public function load_view($view, $data = array())
  {
      // $webRoot = base_url();
      $dompdf = new Dompdf(array('enable_remote' => true));
      $html = $this->ci()->load->view($view, $data, TRUE);
      // $dompdf->setBasePath($webRoot);
      $dompdf->loadHtml($html);
      // $dompdf->setPaper('A4', 'portrait');
      $dompdf->render();
      return $dompdf->output();
  }

  // public function generate($html, $filename='', $stream=TRUE, $paper = 'A4', $orientation = "portrait")
  // {
  //   $dompdf = new DOMPDF();
  //   $dompdf->loadHtml($html);
  //   $dompdf->setPaper($paper, $orientation);
  //   $dompdf->render();
  //   if ($stream) {
  //       $dompdf->stream($filename.".pdf", array("Attachment" => 0));
  //   } else {
  //       return $dompdf->output();
  //   }
  // }
}