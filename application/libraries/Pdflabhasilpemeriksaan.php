<?php
include_once APPPATH . '/third_party/fpdf/fpdf.php';

class Pdflabhasilpemeriksaan extends FPDF
{
  var $widths;
  var $aligns;
  //
  var $widths2;
  var $aligns2;

  var $heights;


  function SetWidths($w)
  {
    //Set the array of column widths
    $this->widths = $w;
  }

  function SetWidths2($w)
  {
    //Set the array of column widths
    $this->widths2 = $w;
  }

  function SetAligns($a)
  {
    //Set the array of column alignments
    $this->aligns = $a;
  }

  function SetAligns2($a)
  {
    //Set the array of column alignments
    $this->aligns2 = $a;
  }

  function SetHeights($h)
  {
    //Set the array of column widths
    $this->heights = $h;
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
      // $this->Rect($x, $y, $w, $h);
      $this->Line($x, $y, $x + $w, $y);
      //Print the text
      $this->MultiCell($w, 5, $data[$i], 0, $a);
      //Put the position to the right of the cell
      $this->SetXY($x + $w, $y);
    }
    //Go to the next line
    $this->Ln($h);
  }

  function Row2($data)
  {
    //Calculate the height of the row
    $nb = 0;
    for ($i = 0; $i < count($data); $i++)
      $nb = max($nb, $this->NbLines($this->widths2[$i], $data[$i]));
    $h = 4 * $nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for ($i = 0; $i < count($data); $i++) {
      $w = $this->widths2[$i];
      $a = isset($this->aligns2[$i]) ? $this->aligns2[$i] : 'L';
      //Save the current position
      $x = $this->GetX();
      $y = $this->GetY();
      //Draw the border
      // $this->Rect($x, $y, $w, $h);
      // $this->Line($x, $y, $x + $w, $y);
      //Print the text
      $this->MultiCell($w, 4, $data[$i], 0, $a);
      //Put the position to the right of the cell
      $this->SetXY($x + $w, $y);
    }
    //Go to the next line
    $this->Ln($h);
  }

  function Row3($data)
  {
    //Calculate the height of the row
    $nb = 0;
    for ($i = 0; $i < count($data); $i++)
      $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
    $height = $this->heights;
    if ($height != '') {
      $h = $height * $nb;
    } else {
      $h = 5 * $nb;
    }
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
    $config = @$GLOBALS['get_config'];
    $image = @$GLOBALS['image'];
    $judul = @$GLOBALS['judul'];
    $identitas = @$GLOBALS['identitas'];
    $diag = @$GLOBALS['diag'];
    $pem = @$GLOBALS['pem'];
    $mn = @$GLOBALS['mn'];
    $pem_id = @$GLOBALS['pem_id'];  

    $dokter_pengirim = '';
    if(@$mn['dokter_pengirim_id'] == '' || @$mn['dokter_pengirim_id'] == NULL) {
      $dokter_pengirim = @$mn['dokter_asal_nm'];
    } else {
      $dokter_pengirim = @$mn['dokter_pengirim_nm'];
    }
    //
    if ($image != '') {
      // $this->Image($image, 60, 80, 90, 0);
      $this->centreImage($image, $orientation);
    }

    $this->Image(FCPATH . 'assets/icons/logo-hesti-wira-sakti.png', $this->GetX(), 8, 0, 23);
    $this->Line(10, 33, ($this->GetPageWidth() - 8), 33);
    $this->Line(10, 33, ($this->GetPageWidth() - 8), 33);
    $this->Line(10, 33.1, ($this->GetPageWidth() - 8), 33.1);
    $this->Line(10, 33.1, ($this->GetPageWidth() - 8), 33.1);
    $this->Line(10, 33.2, ($this->GetPageWidth() - 8), 33.2);
    $this->Line(10, 33.2, ($this->GetPageWidth() - 8), 33.2);
    $this->Line(10, 33.3, ($this->GetPageWidth() - 8), 33.3);
    $this->Line(10, 33.3, ($this->GetPageWidth() - 8), 33.3);
    $this->Line(10, 34, ($this->GetPageWidth() - 8), 34);
    $this->Line(10, 34, ($this->GetPageWidth() - 8), 34);
    $this->Line(10, 34, ($this->GetPageWidth() - 8), 34);
    $this->Line(10, 34, ($this->GetPageWidth() - 8), 34);
    $this->SetFont('Times', 'B', 20);
    $this->Cell(30, 6, '', 0, 0, 'C');
    $this->Cell(0, 6, 'RUMKIT TK III SLAMET RIYADI', 0, 1, 'C');
    $this->SetFont('Arial', '', 10);
    $this->Cell(30, 6, '', 0, 0, 'C');
    $this->Cell(0, 5, 'Jl. Brigjen Slamet Riyadi 321, Surakarta, Jawa Tengah', 0, 1, 'C');
    $this->SetFont('Arial', '', 10);
    $this->Cell(30, 6, '', 0, 0, 'C');
    $this->Cell(0, 4, 'Telp. 0271 714656', 0, 1, 'C');
    $this->Cell(30, 6, '', 0, 0, 'C');
    $this->Cell(0, 4, 'E-mail : rstslametriyadisolo@yahoo.co.id', 0, 1, 'C');

    // $this->Image(FCPATH . 'assets/icons/kop_original.png', 0, 10, 205, 46);
    // $this->Cell(0, 6, '', 0, 1, 'L');
    // $this->Cell(0, 6, '', 0, 1, 'L');
    // $this->Cell(0, 6, '', 0, 1, 'L');

    // $this->Cell(0, 2, '', 0, 1, 'C');
    // $this->Cell(0, 2, '', 0, 1, 'C');
    // $this->Cell(0, 3, '', 0, 1, 'C');

    // $this->Line(10, 57, 200, 57);
    // $this->Line(10, 57, 200, 57);
    // $this->Line(10, 57, 200, 57);
    // $this->Line(10, 57, 200, 57);
    // $this->Line(10, 57, 200, 57);
    // $this->Line(10, 58, 200, 58);
    // $this->Line(10, 58, 200, 58);
    // $this->Line(10, 58, 200, 58);
    // $this->Line(10, 58, 200, 58);

    // $this->Cell(0, 6, '', 0, 1, 'L');

    // $this->Cell(0, 6, '', 0, 1, 'L');
    // $this->Cell(0, 6, '', 0, 1, 'L');
    // $this->Cell(0, 6, '', 0, 1, 'L');
    $this->Cell(0, 10, '', 0, 1, 'L');


    // $this->SetFont('Arial', 'B', 11);
    // $this->Cell(0, 4, @$judul, 0, 1, 'C');
    // $this->Cell(0, 3, '', 0, 1, 'C');
    // $this->Cell(0, 3, '', 0, 1, 'C');

    //Identitas Pasien 2
    $this->SetFont('Arial', '', 9);
    $this->SetWidths2(array('35', '3', '65', '35', '3', '60'));
    $this->SetAligns2(array("L", "C", "L"));

    $this->Row2(array(
      'No. Lab', ':', @$mn['pelayanan_id'],
      'Kategori Pasien', ':', @$mn['penjamin_nm'] . ' ' . ((@$mn['penjamin_id'] == '02') ? '- ' . @$mn['kartu_no'] : ''),
    ));

    $this->Row2(array(
      'No.RM / KTP', ':', @$mn['rm_no'] . ' / ' . @$mn['nik'],
      'Ruang', ':', @$mn['lokasi_asal_nm'],
    ));

    $this->Row2(array(
      'Nama Pasien', ':', strtoupper(mb_convert_encoding(@$mn['pasien_nm'], "UTF-8", "HTML-ENTITIES")) . ', ' . @$mn['sebutan_cd'],
      'Diagnosa', ':', @$diag,
    ));

    $this->Row2(array(
      'TGL /Umur/JK', ':', date('d-m-y', strtotime(@$mn['lahir_tgl'])) . ' /' . @$mn['umur_thn'] . ' Tahun / ' . @$mn['sex_nm'],
      'Tanggal - Jam Terima', ':', @to_date(@$mn['order_tgl'], '-', 'full_date'),
    ));

    $this->Row2(array(
      'Dokter Pengirim', ':',@$dokter_pengirim,
      'Tanggal - Jam Hasil', ':', to_date(@$mn['dilayani_selesai_tgl'], '-', 'full_date'),
    ));

    $this->Cell(0, 2, '', 0, 1, 'C');
    $this->Cell(0, 5, '', 0, 1, 'C');

    $x = $this->GetX();
    $y = $this->GetY();
    $this->Line($x, $y, $x + 190, $y);

    $this->SetFont('Arial', 'B', 10);
    $this->Cell(70, 5, 'PEMERIKSAAN', 1, 0, 'C');
    $this->Cell(40, 5, 'HASIL', 1, 0, 'C');
    $this->Cell(40, 5, 'N.RUJUKAN', 1, 0, 'C');
    $this->Cell(40, 5, 'SATUAN', 1, 1, 'C');

    $x = $this->GetX();
    $y = $this->GetY();
    $this->Line($x, $y, $x + 190, $y);
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

  // function centreImage($img, $orientation = '')
  // {
  //   list($width, $height) = $this->resizeToFit($img);
  //   //
  //   if ($orientation == 'P') {
  //     $orientation_width = $this->h;
  //     $orientation_height = $this->w;
  //   } elseif ($orientation == 'L') {
  //     $orientation_width = $this->w;
  //     $orientation_height = $this->h;
  //   } else {
  //     $orientation_width = $this->h;
  //     $orientation_height = $this->w;
  //   }
  //   //
  //   $this->Image(
  //     $img,
  //     ($orientation_height - $width) / 2,
  //     ($orientation_width - $height) / 2,
  //     $width,
  //     $height
  //   );
  // }

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
    } elseif ($orientation == 'KOP') {
      $orientation_width = 38;
      $orientation_height = $this->w;
    } else {
      $orientation_width = $this->h;
      $orientation_height = $this->w;
    }
    //
    if ($orientation != '') {
      $this->Image(
        $img,
        ($orientation_height - $width) / 2,
        ($orientation_width - $height) / 2,
        $width,
        $height
      );
    } else {
      $this->Image(
        $img,
        ($this->w - $width) / 2,
        ($this->h - $height) / 2,
        $width,
        $height
      );
    }
  }

  function Footer()
  {

    $config = @$GLOBALS['get_config'];
    $image = @$GLOBALS['image'];
    $judul = @$GLOBALS['judul'];
    $identitas = @$GLOBALS['identitas'];
    $diag = @$GLOBALS['diag'];
    $pem = @$GLOBALS['pem'];
    $mn = @$GLOBALS['mn'];
    $pem_id = @$GLOBALS['pem_id'];

    $this->SetY(-20);
    $this->SetFont('Arial', 'B', 10);
    $this->Cell(0, 2, '', 0, 1, 'C');
    $this->Cell(0, 5, '', 0, 1, 'C');

    $x = $this->GetX();
    $y = $this->GetY();
    $this->Line($x, $y, $x + 190, $y);
    $this->Cell(30, 10, 'Dicetak Oleh :', 0, 0, 'L');
    $this->Cell(105, 10, @$pem['petugaspemeriksa_nm'], 0, 0, 'L');
    $this->SetFont('Arial', 'B', 10);
    $this->Cell(20, 10, 'Tgl.Cetak :', 0, 0, 'L');
    $this->Cell(50, 10, date('d/m/Y H:i:s'), 0, 0, 'L');
  }
}
