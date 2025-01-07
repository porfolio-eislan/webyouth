<?php
include_once APPPATH . '/third_party/fpdf/fpdf.php';

class Pdfnew extends FPDF
{
	var $widths;
	var $heights;
	var $aligns;

	function SetWidths($w)
	{
		//Set the array of column widths
		$this->widths = $w;
	}

	function SetHeights($h)
	{
		//Set the array of column widths
		$this->heights = $h;
	}

	function SetAligns($a)
	{
		//Set the array of column alignments
		$this->aligns = $a;
	}

	function Row($data, $fill = false)
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
			$this->MultiCell($w, $height, $data[$i], 0, $a, $fill);
			//Put the position to the right of the cell
			$this->SetXY($x + $w, $y);
		}
		//Go to the next line
		$this->Ln($h);
	}

	function RoundedRect($x, $y, $w, $h, $r, $style = '')
	{
		$k = $this->k;
		$hp = $this->h;
		if ($style == 'F')
			$op = 'f';
		elseif ($style == 'FD' || $style == 'DF')
			$op = 'B';
		else
			$op = 'S';
		$MyArc = 4 / 3 * (sqrt(2) - 1);
		$this->_out(sprintf('%.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k));
		$xc = $x + $w - $r;
		$yc = $y + $r;
		$this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - $y) * $k));

		$this->_Arc($xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc);
		$xc = $x + $w - $r;
		$yc = $y + $h - $r;
		$this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k));
		$this->_Arc($xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r);
		$xc = $x + $r;
		$yc = $y + $h - $r;
		$this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k));
		$this->_Arc($xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc);
		$xc = $x + $r;
		$yc = $y + $r;
		$this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - $yc) * $k));
		$this->_Arc($xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r);
		$this->_out($op);
	}

	function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
	{
		$h = $this->h;
		$this->_out(sprintf(
			'%.2F %.2F %.2F %.2F %.2F %.2F c ',
			$x1 * $this->k,
			($h - $y1) * $this->k,
			$x2 * $this->k,
			($h - $y2) * $this->k,
			$x3 * $this->k,
			($h - $y3) * $this->k
		));
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
		// //Put the watermark
		// $image = FCPATH . '/assets/images/icon/watermark.png';
		// //
		// if ($image != '') {
		// 	// $this->Image($image, 60, 80, 90, 0);
		// 	$this->centreImage($image, $orientation);
		// }
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
	// function Footer()
	// {
	//     // Position at 1.5 cm from bottom
	//     $this->SetY(-15);
	//     // Arial italic 8
	//     $this->SetFont('Arial','I',8);
	//     // Page number
	//     $this->Cell(0,10,'Halaman '.$this->PageNo().'/{nb}',0,0,'R');
	// }

	function drawTextBox($strText, $w, $h, $align = 'L', $valign = 'T', $border = true, $ln = 0, $y = '')
	{
		$xi = $this->GetX();
		if ($y == '') {
			$yi = $this->GetY();
		} else {
			$yi = $y;
		}

		$hrow = $this->FontSize;
		$textrows = $this->drawRows($w, $hrow, $strText, 0, $align, 0, 0, 0);
		$maxrows = floor($h / $this->FontSize);
		$rows = min($textrows, $maxrows);

		$dy = 0;
		if (strtoupper($valign) == 'M')
			$dy = ($h - $rows * $this->FontSize) / 2;
		if (strtoupper($valign) == 'B')
			$dy = $h - $rows * $this->FontSize;

		$this->SetY($yi + $dy);
		$this->SetX($xi);

		$this->drawRows($w, $hrow, $strText, 0, $align, false, $rows, 1, $ln);

		if ($border)
			$this->Rect($xi, $yi, $w, $h);
	}

	function drawRows($w, $h, $txt, $border = 0, $align = 'J', $fill = false, $maxline = 0, $prn = 0, $ln = 0)
	{
		$cw = &$this->CurrentFont['cw'];
		if ($w == 0)
			$w = $this->w - $this->rMargin - $this->x;
		$wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
		$s = str_replace("\r", '', $txt);
		$nb = strlen($s);
		if ($nb > 0 && $s[$nb - 1] == "\n")
			$nb--;
		$b = 0;
		if ($border) {
			if ($border == 1) {
				$border = 'LTRB';
				$b = 'LRT';
				$b2 = 'LR';
			} else {
				$b2 = '';
				if (is_int(strpos($border, 'L')))
					$b2 .= 'L';
				if (is_int(strpos($border, 'R')))
					$b2 .= 'R';
				$b = is_int(strpos($border, 'T')) ? $b2 . 'T' : $b2;
			}
		}
		$sep = -1;
		$i = 0;
		$j = 0;
		$l = 0;
		$ns = 0;
		$nl = 1;
		while ($i < $nb) {
			//Get next character
			$c = $s[$i];
			if ($c == "\n") {
				//Explicit line break
				if ($this->ws > 0) {
					$this->ws = 0;
					if ($prn == 1) $this->_out('0 Tw');
				}
				if ($prn == 1) {
					$this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
				}
				$i++;
				$sep = -1;
				$j = $i;
				$l = 0;
				$ns = 0;
				$nl++;
				if ($border && $nl == 2)
					$b = $b2;
				if ($maxline && $nl > $maxline)
					return substr($s, $i);
				continue;
			}
			if ($c == ' ') {
				$sep = $i;
				$ls = $l;
				$ns++;
			}
			$l += $cw[$c];
			if ($l > $wmax) {
				//Automatic line break
				if ($sep == -1) {
					if ($i == $j)
						$i++;
					if ($this->ws > 0) {
						$this->ws = 0;
						if ($prn == 1) $this->_out('0 Tw');
					}
					if ($prn == 1) {
						$this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
					}
				} else {
					if ($align == 'J') {
						$this->ws = ($ns > 1) ? ($wmax - $ls) / 1000 * $this->FontSize / ($ns - 1) : 0;
						if ($prn == 1) $this->_out(sprintf('%.3F Tw', $this->ws * $this->k));
					}
					if ($prn == 1) {
						$this->Cell($w, $h, substr($s, $j, $sep - $j), $b, 2, $align, $fill);
					}
					$i = $sep + 1;
				}
				$sep = -1;
				$j = $i;
				$l = 0;
				$ns = 0;
				$nl++;
				if ($border && $nl == 2)
					$b = $b2;
				if ($maxline && $nl > $maxline)
					return substr($s, $i);
			} else
				$i++;
		}
		//Last chunk
		if ($this->ws > 0) {
			$this->ws = 0;
			if ($prn == 1) $this->_out('0 Tw');
		}
		if ($border && is_int(strpos($border, 'B')))
			$b .= 'B';
		if ($prn == 1) {
			$this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
		}
		// $this->x = $this->lMargin;
		if ($ln > 0) {
			// Go to next line
			$this->y += $h;
			if ($ln == 1)
				$this->x = $this->lMargin;
		} else
			$this->x += $w;
		return $nl;
	}

	function TextWithDirection($x, $y, $txt, $direction = 'R')
	{
		if ($direction == 'R')
			$s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', 1, 0, 0, 1, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
		elseif ($direction == 'L')
			$s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', -1, 0, 0, -1, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
		elseif ($direction == 'U')
			$s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', 0, 1, -1, 0, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
		elseif ($direction == 'D')
			$s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', 0, -1, 1, 0, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
		else
			$s = sprintf('BT %.2F %.2F Td (%s) Tj ET', $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
		if ($this->ColorFlag)
			$s = 'q ' . $this->TextColor . ' ' . $s . ' Q';
		$this->_out($s);
	}

	function TextWithRotation($x, $y, $txt, $txt_angle, $font_angle = 0)
	{
		$font_angle += 90 + $txt_angle;
		$txt_angle *= M_PI / 180;
		$font_angle *= M_PI / 180;

		$txt_dx = cos($txt_angle);
		$txt_dy = sin($txt_angle);
		$font_dx = cos($font_angle);
		$font_dy = sin($font_angle);

		$s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', $txt_dx, $txt_dy, $font_dx, $font_dy, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
		if ($this->ColorFlag)
			$s = 'q ' . $this->TextColor . ' ' . $s . ' Q';
		$this->_out($s);
	}
}
