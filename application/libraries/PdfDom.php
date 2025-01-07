<?php defined('BASEPATH') or exit('No direct script access allowed');
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfDom
{
  public function generate($html, $filename = '',  $paper = '', $orientation = '', $stream = TRUE)
  {
    $options = new Options();
    $options->set('isRemoteEnabled', TRUE);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper($paper, $orientation);
    $dompdf->render();
    if ($stream) {
      $dompdf->stream($filename . ".pdf", array("Attachment" => 0));
      exit();
    } else {
      return $dompdf->output();
    }
  }

  public function merge($html, $filename, $paper = '', $orientation = '', $path)
  {
    $options = new Options();
    $options->set('isRemoteEnabled', TRUE);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper($paper, $orientation);
    $dompdf->render();
    $save = $dompdf->output();

    if (!is_dir($path)) {
      mkdir($path, 0777, true);
    }

    if (is_writable($path)) {
      file_put_contents($path . '/' . $filename  . ".pdf", $save);
      return base_url() . $path . '/' . $filename  . ".pdf";
    } else {
      die("Error: The directory is not writable.");
    }
  }
}
