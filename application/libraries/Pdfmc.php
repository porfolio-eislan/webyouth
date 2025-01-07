<?php
include_once APPPATH . '/third_party/fpdf/fpdf.php';

class Pdfmc extends FPDF
{
  var $widths;
  var $aligns;

  function SetWidths($w)
  {
    //Set the array of column widths
    $this->widths = $w;
  }

  function SetAligns($a)
  {
    //Set the array of column alignments
    $this->aligns = $a;
  }

  function Row($data)
  {
    //Calculate the height of the row
    $nb = 0;
    for ($i = 0; $i < count($data); $i++)
      $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
    $h = 5 * $nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for ($i = 0; $i < count($data); $i++) {
      $w = $this->widths[$i];
      $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
      //Save the current position
      $x = $this->GetX();
      $y = $this->GetY();
      //Draw the border
      $this->Rect($x, $y, $w, $h);
      //Print the text
      $this->MultiCell($w, 5, $data[$i], 0, $a);
      //Put the position to the right of the cell
      $this->SetXY($x + $w, $y);
    }
    //Go to the next line
    $this->Ln($h);
  }

  function CheckPageBreak($h)
  {
    //If the height h would cause an overflow, add a new page immediately
    if ($this->GetY() + $h > $this->PageBreakTrigger)
      $this->AddPage($this->CurOrientation);
  }

  function NbLines($w, $txt)
  {
    //Computes the number of lines a MultiCell of width w will take
    $cw = &$this->CurrentFont['cw'];
    if ($w == 0)
      $w = $this->w - $this->rMargin - $this->x;
    $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
    $s = str_replace("\r", '', $txt);
    $nb = strlen($s);
    if ($nb > 0 and $s[$nb - 1] == "\n")
      $nb--;
    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $nl = 1;
    while ($i < $nb) {
      $c = $s[$i];
      if ($c == "\n") {
        $i++;
        $sep = -1;
        $j = $i;
        $l = 0;
        $nl++;
        continue;
      }
      if ($c == ' ')
        $sep = $i;
      $l += $cw[$c];
      if ($l > $wmax) {
        if ($sep == -1) {
          if ($i == $j)
            $i++;
        } else
          $i = $sep + 1;
        $sep = -1;
        $j = $i;
        $l = 0;
        $nl++;
      } else
        $i++;
    }
    return $nl;
  }

  function Header($orientation = '')
  {
    //Put the watermark
    $image = $GLOBALS['image'];
    //
    if ($image != '') {
      // $this->Image($image, 60, 80, 90, 0);
      $this->centreImage($image, $orientation);
    }
  }

  const DPI = 100;
  const MM_IN_INCH = 25.4;
  // tweak these values (in pixels)
  const MAX_WIDTH = 800;
  const MAX_HEIGHT = 500;

  function pixelsToMM($val)
  {
    return $val * self::MM_IN_INCH / self::DPI;
  }

  function resizeToFit($imgFilename)
  {
    list($width, $height) = getimagesize($imgFilename);
    $widthScale = self::MAX_WIDTH / $width;
    $heightScale = self::MAX_HEIGHT / $height;
    $scale = min($widthScale, $heightScale);
    return array(
      round($this->pixelsToMM($scale * $width)),
      round($this->pixelsToMM($scale * $height))
    );
  }

  function centreImage($img, $orientation = '')
  {
    list($width, $height) = $this->resizeToFit($img);
    //
    if ($orientation == 'P') {
      $orientation_width = $this->h;
      $orientation_height = $this->w;
    } elseif ($orientation == 'L') {
      $orientation_width = $this->w;
      $orientation_height = $this->h;
    } else {
      $orientation_width = $this->h;
      $orientation_height = $this->w;
    }
    //
    $this->Image(
      $img,
      ($orientation_height - $width) / 2,
      ($orientation_width - $height) / 2,
      $width,
      $height
    );
  }
}
