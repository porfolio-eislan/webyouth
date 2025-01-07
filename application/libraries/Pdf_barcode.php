<?php
include_once APPPATH . '/third_party/fpdf/fpdf.php';
class PDF_BARCODE extends FPDF
{
  protected $T128;                                         // Tableau des codes 128
  protected $ABCset = "";                                  // jeu des caractères éligibles au C128
  protected $Aset = "";                                    // Set A du jeu des caractères éligibles
  protected $Bset = "";                                    // Set B du jeu des caractères éligibles
  protected $Cset = "";                                    // Set C du jeu des caractères éligibles
  protected $SetFrom;                                      // Convertisseur source des jeux vers le tableau
  protected $SetTo;                                        // Convertisseur destination des jeux vers le tableau
  protected $JStart = array("A" => 103, "B" => 104, "C" => 105); // Caractères de sélection de jeu au début du C128
  protected $JSwap = array("A" => 101, "B" => 100, "C" => 99);   // Caractères de changement de jeu
  var $widths;
  var $heights;
  var $aligns;

  //____________________________ Extension du constructeur _______________________
  function __construct($orientation = 'P', $unit = 'mm', $format = 'A4')
  {

    parent::__construct($orientation, $unit, $format);

    $this->T128[] = array(2, 1, 2, 2, 2, 2);           //0 : [ ]               // composition des caractères
    $this->T128[] = array(2, 2, 2, 1, 2, 2);           //1 : [!]
    $this->T128[] = array(2, 2, 2, 2, 2, 1);           //2 : ["]
    $this->T128[] = array(1, 2, 1, 2, 2, 3);           //3 : [#]
    $this->T128[] = array(1, 2, 1, 3, 2, 2);           //4 : [$]
    $this->T128[] = array(1, 3, 1, 2, 2, 2);           //5 : [%]
    $this->T128[] = array(1, 2, 2, 2, 1, 3);           //6 : [&]
    $this->T128[] = array(1, 2, 2, 3, 1, 2);           //7 : [']
    $this->T128[] = array(1, 3, 2, 2, 1, 2);           //8 : [(]
    $this->T128[] = array(2, 2, 1, 2, 1, 3);           //9 : [)]
    $this->T128[] = array(2, 2, 1, 3, 1, 2);           //10 : [*]
    $this->T128[] = array(2, 3, 1, 2, 1, 2);           //11 : [+]
    $this->T128[] = array(1, 1, 2, 2, 3, 2);           //12 : [,]
    $this->T128[] = array(1, 2, 2, 1, 3, 2);           //13 : [-]
    $this->T128[] = array(1, 2, 2, 2, 3, 1);           //14 : [.]
    $this->T128[] = array(1, 1, 3, 2, 2, 2);           //15 : [/]
    $this->T128[] = array(1, 2, 3, 1, 2, 2);           //16 : [0]
    $this->T128[] = array(1, 2, 3, 2, 2, 1);           //17 : [1]
    $this->T128[] = array(2, 2, 3, 2, 1, 1);           //18 : [2]
    $this->T128[] = array(2, 2, 1, 1, 3, 2);           //19 : [3]
    $this->T128[] = array(2, 2, 1, 2, 3, 1);           //20 : [4]
    $this->T128[] = array(2, 1, 3, 2, 1, 2);           //21 : [5]
    $this->T128[] = array(2, 2, 3, 1, 1, 2);           //22 : [6]
    $this->T128[] = array(3, 1, 2, 1, 3, 1);           //23 : [7]
    $this->T128[] = array(3, 1, 1, 2, 2, 2);           //24 : [8]
    $this->T128[] = array(3, 2, 1, 1, 2, 2);           //25 : [9]
    $this->T128[] = array(3, 2, 1, 2, 2, 1);           //26 : [:]
    $this->T128[] = array(3, 1, 2, 2, 1, 2);           //27 : [;]
    $this->T128[] = array(3, 2, 2, 1, 1, 2);           //28 : [<]
    $this->T128[] = array(3, 2, 2, 2, 1, 1);           //29 : [=]
    $this->T128[] = array(2, 1, 2, 1, 2, 3);           //30 : [>]
    $this->T128[] = array(2, 1, 2, 3, 2, 1);           //31 : [?]
    $this->T128[] = array(2, 3, 2, 1, 2, 1);           //32 : [@]
    $this->T128[] = array(1, 1, 1, 3, 2, 3);           //33 : [A]
    $this->T128[] = array(1, 3, 1, 1, 2, 3);           //34 : [B]
    $this->T128[] = array(1, 3, 1, 3, 2, 1);           //35 : [C]
    $this->T128[] = array(1, 1, 2, 3, 1, 3);           //36 : [D]
    $this->T128[] = array(1, 3, 2, 1, 1, 3);           //37 : [E]
    $this->T128[] = array(1, 3, 2, 3, 1, 1);           //38 : [F]
    $this->T128[] = array(2, 1, 1, 3, 1, 3);           //39 : [G]
    $this->T128[] = array(2, 3, 1, 1, 1, 3);           //40 : [H]
    $this->T128[] = array(2, 3, 1, 3, 1, 1);           //41 : [I]
    $this->T128[] = array(1, 1, 2, 1, 3, 3);           //42 : [J]
    $this->T128[] = array(1, 1, 2, 3, 3, 1);           //43 : [K]
    $this->T128[] = array(1, 3, 2, 1, 3, 1);           //44 : [L]
    $this->T128[] = array(1, 1, 3, 1, 2, 3);           //45 : [M]
    $this->T128[] = array(1, 1, 3, 3, 2, 1);           //46 : [N]
    $this->T128[] = array(1, 3, 3, 1, 2, 1);           //47 : [O]
    $this->T128[] = array(3, 1, 3, 1, 2, 1);           //48 : [P]
    $this->T128[] = array(2, 1, 1, 3, 3, 1);           //49 : [Q]
    $this->T128[] = array(2, 3, 1, 1, 3, 1);           //50 : [R]
    $this->T128[] = array(2, 1, 3, 1, 1, 3);           //51 : [S]
    $this->T128[] = array(2, 1, 3, 3, 1, 1);           //52 : [T]
    $this->T128[] = array(2, 1, 3, 1, 3, 1);           //53 : [U]
    $this->T128[] = array(3, 1, 1, 1, 2, 3);           //54 : [V]
    $this->T128[] = array(3, 1, 1, 3, 2, 1);           //55 : [W]
    $this->T128[] = array(3, 3, 1, 1, 2, 1);           //56 : [X]
    $this->T128[] = array(3, 1, 2, 1, 1, 3);           //57 : [Y]
    $this->T128[] = array(3, 1, 2, 3, 1, 1);           //58 : [Z]
    $this->T128[] = array(3, 3, 2, 1, 1, 1);           //59 : [[]
    $this->T128[] = array(3, 1, 4, 1, 1, 1);           //60 : [\]
    $this->T128[] = array(2, 2, 1, 4, 1, 1);           //61 : []]
    $this->T128[] = array(4, 3, 1, 1, 1, 1);           //62 : [^]
    $this->T128[] = array(1, 1, 1, 2, 2, 4);           //63 : [_]
    $this->T128[] = array(1, 1, 1, 4, 2, 2);           //64 : [`]
    $this->T128[] = array(1, 2, 1, 1, 2, 4);           //65 : [a]
    $this->T128[] = array(1, 2, 1, 4, 2, 1);           //66 : [b]
    $this->T128[] = array(1, 4, 1, 1, 2, 2);           //67 : [c]
    $this->T128[] = array(1, 4, 1, 2, 2, 1);           //68 : [d]
    $this->T128[] = array(1, 1, 2, 2, 1, 4);           //69 : [e]
    $this->T128[] = array(1, 1, 2, 4, 1, 2);           //70 : [f]
    $this->T128[] = array(1, 2, 2, 1, 1, 4);           //71 : [g]
    $this->T128[] = array(1, 2, 2, 4, 1, 1);           //72 : [h]
    $this->T128[] = array(1, 4, 2, 1, 1, 2);           //73 : [i]
    $this->T128[] = array(1, 4, 2, 2, 1, 1);           //74 : [j]
    $this->T128[] = array(2, 4, 1, 2, 1, 1);           //75 : [k]
    $this->T128[] = array(2, 2, 1, 1, 1, 4);           //76 : [l]
    $this->T128[] = array(4, 1, 3, 1, 1, 1);           //77 : [m]
    $this->T128[] = array(2, 4, 1, 1, 1, 2);           //78 : [n]
    $this->T128[] = array(1, 3, 4, 1, 1, 1);           //79 : [o]
    $this->T128[] = array(1, 1, 1, 2, 4, 2);           //80 : [p]
    $this->T128[] = array(1, 2, 1, 1, 4, 2);           //81 : [q]
    $this->T128[] = array(1, 2, 1, 2, 4, 1);           //82 : [r]
    $this->T128[] = array(1, 1, 4, 2, 1, 2);           //83 : [s]
    $this->T128[] = array(1, 2, 4, 1, 1, 2);           //84 : [t]
    $this->T128[] = array(1, 2, 4, 2, 1, 1);           //85 : [u]
    $this->T128[] = array(4, 1, 1, 2, 1, 2);           //86 : [v]
    $this->T128[] = array(4, 2, 1, 1, 1, 2);           //87 : [w]
    $this->T128[] = array(4, 2, 1, 2, 1, 1);           //88 : [x]
    $this->T128[] = array(2, 1, 2, 1, 4, 1);           //89 : [y]
    $this->T128[] = array(2, 1, 4, 1, 2, 1);           //90 : [z]
    $this->T128[] = array(4, 1, 2, 1, 2, 1);           //91 : [{]
    $this->T128[] = array(1, 1, 1, 1, 4, 3);           //92 : [|]
    $this->T128[] = array(1, 1, 1, 3, 4, 1);           //93 : [}]
    $this->T128[] = array(1, 3, 1, 1, 4, 1);           //94 : [~]
    $this->T128[] = array(1, 1, 4, 1, 1, 3);           //95 : [DEL]
    $this->T128[] = array(1, 1, 4, 3, 1, 1);           //96 : [FNC3]
    $this->T128[] = array(4, 1, 1, 1, 1, 3);           //97 : [FNC2]
    $this->T128[] = array(4, 1, 1, 3, 1, 1);           //98 : [SHIFT]
    $this->T128[] = array(1, 1, 3, 1, 4, 1);           //99 : [Cswap]
    $this->T128[] = array(1, 1, 4, 1, 3, 1);           //100 : [Bswap]                
    $this->T128[] = array(3, 1, 1, 1, 4, 1);           //101 : [Aswap]
    $this->T128[] = array(4, 1, 1, 1, 3, 1);           //102 : [FNC1]
    $this->T128[] = array(2, 1, 1, 4, 1, 2);           //103 : [Astart]
    $this->T128[] = array(2, 1, 1, 2, 1, 4);           //104 : [Bstart]
    $this->T128[] = array(2, 1, 1, 2, 3, 2);           //105 : [Cstart]
    $this->T128[] = array(2, 3, 3, 1, 1, 1);           //106 : [STOP]
    $this->T128[] = array(2, 1);                       //107 : [END BAR]

    for ($i = 32; $i <= 95; $i++) {                                            // jeux de caractères
      $this->ABCset .= chr($i);
    }
    $this->Aset = $this->ABCset;
    $this->Bset = $this->ABCset;

    for ($i = 0; $i <= 31; $i++) {
      $this->ABCset .= chr($i);
      $this->Aset .= chr($i);
    }
    for ($i = 96; $i <= 127; $i++) {
      $this->ABCset .= chr($i);
      $this->Bset .= chr($i);
    }
    for ($i = 200; $i <= 210; $i++) {                                           // controle 128
      $this->ABCset .= chr($i);
      $this->Aset .= chr($i);
      $this->Bset .= chr($i);
    }
    $this->Cset = "0123456789" . chr(206);

    for ($i = 0; $i < 96; $i++) {                                                   // convertisseurs des jeux A & B
      @$this->SetFrom["A"] .= chr($i);
      @$this->SetFrom["B"] .= chr($i + 32);
      @$this->SetTo["A"] .= chr(($i < 32) ? $i + 64 : $i - 32);
      @$this->SetTo["B"] .= chr($i);
    }
    for ($i = 96; $i < 107; $i++) {                                                 // contrôle des jeux A & B
      @$this->SetFrom["A"] .= chr($i + 104);
      @$this->SetFrom["B"] .= chr($i + 104);
      @$this->SetTo["A"] .= chr($i);
      @$this->SetTo["B"] .= chr($i);
    }
  }

  function barcode($xpos, $ypos, $code, $basewidth = 1, $height = 10)
  {

    $wide = $basewidth;
    $narrow = $basewidth / 3;

    // wide/narrow codes for the digits
    $barChar['0'] = 'nnwwn';
    $barChar['1'] = 'wnnnw';
    $barChar['2'] = 'nwnnw';
    $barChar['3'] = 'wwnnn';
    $barChar['4'] = 'nnwnw';
    $barChar['5'] = 'wnwnn';
    $barChar['6'] = 'nwwnn';
    $barChar['7'] = 'nnnww';
    $barChar['8'] = 'wnnwn';
    $barChar['9'] = 'nwnwn';
    $barChar['A'] = 'nn';
    $barChar['Z'] = 'wn';

    // add leading zero if code-length is odd
    if (strlen($code) % 2 != 0) {
      $code = '0' . $code;
    }

    // $this->SetFont('Arial', '', 10);
    // $this->Text($xpos, $ypos + $height + 4, $code);
    // $this->SetFillColor(0);

    // add start and stop codes
    $code = 'AA' . strtolower($code) . 'ZA';

    for ($i = 0; $i < strlen($code); $i = $i + 2) {
      // choose next pair of digits
      $charBar = $code[$i];
      $charSpace = $code[$i + 1];
      // check whether it is a valid digit
      if (!isset($barChar[$charBar])) {
        $this->Error('Invalid character in barcode: ' . $charBar);
      }
      if (!isset($barChar[$charSpace])) {
        $this->Error('Invalid character in barcode: ' . $charSpace);
      }
      // create a wide/narrow-sequence (first digit=bars, second digit=spaces)
      $seq = '';
      for ($s = 0; $s < strlen($barChar[$charBar]); $s++) {
        $seq .= $barChar[$charBar][$s] . $barChar[$charSpace][$s];
      }
      for ($bar = 0; $bar < strlen($seq); $bar++) {
        // set lineWidth depending on value
        if ($seq[$bar] == 'n') {
          $lineWidth = $narrow;
        } else {
          $lineWidth = $wide;
        }
        // draw every second value, because the second digit of the pair is represented by the spaces
        if ($bar % 2 == 0) {
          $this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
        }
        $xpos += $lineWidth;
      }
    }
  }

  function barcode39($xpos, $ypos, $code, $baseline = 0.5, $height = 5)
  {

    $wide = $baseline;
    $narrow = $baseline / 3;
    $gap = $narrow;

    $barChar['0'] = 'nnnwwnwnn';
    $barChar['1'] = 'wnnwnnnnw';
    $barChar['2'] = 'nnwwnnnnw';
    $barChar['3'] = 'wnwwnnnnn';
    $barChar['4'] = 'nnnwwnnnw';
    $barChar['5'] = 'wnnwwnnnn';
    $barChar['6'] = 'nnwwwnnnn';
    $barChar['7'] = 'nnnwnnwnw';
    $barChar['8'] = 'wnnwnnwnn';
    $barChar['9'] = 'nnwwnnwnn';
    $barChar['A'] = 'wnnnnwnnw';
    $barChar['B'] = 'nnwnnwnnw';
    $barChar['C'] = 'wnwnnwnnn';
    $barChar['D'] = 'nnnnwwnnw';
    $barChar['E'] = 'wnnnwwnnn';
    $barChar['F'] = 'nnwnwwnnn';
    $barChar['G'] = 'nnnnnwwnw';
    $barChar['H'] = 'wnnnnwwnn';
    $barChar['I'] = 'nnwnnwwnn';
    $barChar['J'] = 'nnnnwwwnn';
    $barChar['K'] = 'wnnnnnnww';
    $barChar['L'] = 'nnwnnnnww';
    $barChar['M'] = 'wnwnnnnwn';
    $barChar['N'] = 'nnnnwnnww';
    $barChar['O'] = 'wnnnwnnwn';
    $barChar['P'] = 'nnwnwnnwn';
    $barChar['Q'] = 'nnnnnnwww';
    $barChar['R'] = 'wnnnnnwwn';
    $barChar['S'] = 'nnwnnnwwn';
    $barChar['T'] = 'nnnnwnwwn';
    $barChar['U'] = 'wwnnnnnnw';
    $barChar['V'] = 'nwwnnnnnw';
    $barChar['W'] = 'wwwnnnnnn';
    $barChar['X'] = 'nwnnwnnnw';
    $barChar['Y'] = 'wwnnwnnnn';
    $barChar['Z'] = 'nwwnwnnnn';
    $barChar['-'] = 'nwnnnnwnw';
    $barChar['.'] = 'wwnnnnwnn';
    $barChar[' '] = 'nwwnnnwnn';
    $barChar['*'] = 'nwnnwnwnn';
    $barChar['$'] = 'nwnwnwnnn';
    $barChar['/'] = 'nwnwnnnwn';
    $barChar['+'] = 'nwnnnwnwn';
    $barChar['%'] = 'nnnwnwnwn';

    $this->SetFont('Arial', '', 10);
    // $this->Text($xpos, $ypos + $height + 4, $code);
    $this->SetFillColor(0);

    $code = '*' . strtoupper($code) . '*';
    for ($i = 0; $i < strlen($code); $i++) {
      $char = $code[$i];
      if (!isset($barChar[$char])) {
        $this->Error('Invalid character in barcode: ' . $char);
      }
      $seq = $barChar[$char];
      for ($bar = 0; $bar < 9; $bar++) {
        if ($seq[$bar] == 'n') {
          $lineWidth = $narrow;
        } else {
          $lineWidth = $wide;
        }
        if ($bar % 2 == 0) {
          $this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
        }
        $xpos += $lineWidth;
      }
      $xpos += $gap;
    }
  }

  function barcode128($x, $y, $code, $w, $h)
  {
    $Aguid = "";                                                                      // Création des guides de choix ABC
    $Bguid = "";
    $Cguid = "";
    for ($i = 0; $i < strlen($code); $i++) {
      $needle = substr($code, $i, 1);
      $Aguid .= ((strpos($this->Aset, $needle) === false) ? "N" : "O");
      $Bguid .= ((strpos($this->Bset, $needle) === false) ? "N" : "O");
      $Cguid .= ((strpos($this->Cset, $needle) === false) ? "N" : "O");
    }

    $SminiC = "OOOO";
    $IminiC = 4;

    $crypt = "";
    while ($code > "") {
      // BOUCLE PRINCIPALE DE CODAGE
      $i = strpos($Cguid, $SminiC);                                                // forçage du jeu C, si possible
      if ($i !== false) {
        $Aguid[$i] = "N";
        $Bguid[$i] = "N";
      }

      if (substr($Cguid, 0, $IminiC) == $SminiC) {                                  // jeu C
        $crypt .= chr(($crypt > "") ? $this->JSwap["C"] : $this->JStart["C"]);  // début Cstart, sinon Cswap
        $made = strpos($Cguid, "N");                                             // étendu du set C
        if ($made === false) {
          $made = strlen($Cguid);
        }
        if (fmod($made, 2) == 1) {
          $made--;                                                            // seulement un nombre pair
        }
        for ($i = 0; $i < $made; $i += 2) {
          $crypt .= chr(strval(substr($code, $i, 2)));                          // conversion 2 par 2
        }
        $jeu = "C";
      } else {
        $madeA = strpos($Aguid, "N");                                            // étendu du set A
        if ($madeA === false) {
          $madeA = strlen($Aguid);
        }
        $madeB = strpos($Bguid, "N");                                            // étendu du set B
        if ($madeB === false) {
          $madeB = strlen($Bguid);
        }
        $made = (($madeA < $madeB) ? $madeB : $madeA);                         // étendu traitée
        $jeu = (($madeA < $madeB) ? "B" : "A");                                // Jeu en cours

        $crypt .= chr(($crypt > "") ? $this->JSwap[$jeu] : $this->JStart[$jeu]); // début start, sinon swap

        $crypt .= strtr(substr($code, 0, $made), $this->SetFrom[$jeu], $this->SetTo[$jeu]); // conversion selon jeu

      }
      $code = substr($code, $made);                                           // raccourcir légende et guides de la zone traitée
      $Aguid = substr($Aguid, $made);
      $Bguid = substr($Bguid, $made);
      $Cguid = substr($Cguid, $made);
    }                                                                          // FIN BOUCLE PRINCIPALE

    $check = ord($crypt[0]);                                                   // calcul de la somme de contrôle
    for ($i = 0; $i < strlen($crypt); $i++) {
      $check += (ord($crypt[$i]) * $i);
    }
    $check %= 103;

    $crypt .= chr($check) . chr(106) . chr(107);                               // Chaine cryptée complète

    $i = (strlen($crypt) * 11) - 8;                                            // calcul de la largeur du module
    $modul = $w / $i;

    for ($i = 0; $i < strlen($crypt); $i++) {                                      // BOUCLE D'IMPRESSION
      $c = $this->T128[ord($crypt[$i])];
      for ($j = 0; $j < count($c); $j++) {
        $this->Rect($x, $y, $c[$j] * $modul, $h, "F");
        $x += ($c[$j++] + $c[$j]) * $modul;
      }
    }
  }

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

  function FancyRow($data, $border = array(), $align = array(), $font_style = array(), $font_weight = array(), $font_size = array(), $fill_color = array(), $maxline = array())
  {
    // $str_height = 5;
    //Calculate the height of the row
    $nb = 0;
    for ($i = 0; $i < count($data); $i++) {
      $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
    }
    if (count($maxline)) {
      $_maxline = max($maxline);
      if ($nb > $_maxline) {
        $nb = $_maxline;
      }
    }
    $h = ((int)@$this->heights ?: 5) * $nb;
    // print_r(count($maxline));
    // print_r('<br>');
    // $h = 5 * $nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for ($i = 0; $i < count($data); $i++) {
      $w = $this->widths[$i];
      // alignment
      $a = isset($align[$i]) ? $align[$i] : 'L';
      // maxline
      $m = isset($maxline[$i]) ? $maxline[$i] : false;
      //Save the current position
      $x = $this->GetX();
      $y = $this->GetY();
      //Draw the border
      if ($border[$i] == 1) {
        $this->Rect($x, $y, $w, $h);
      } else {
        $_border = strtoupper($border[$i]);
        if (strstr($_border, 'L') !== false) {
          $this->Line($x, $y, $x, $y + $h);
        }
        if (strstr($_border, 'R') !== false) {
          $this->Line($x + $w, $y, $x + $w, $y + $h);
        }
        if (strstr($_border, 'T') !== false) {
          $this->Line($x, $y, $x + $w, $y);
        }
        if (strstr($_border, 'B') !== false) {
          $this->Line($x, $y + $h, $x + $w, $y + $h);
        }
      }
      // Setting Cell Fill Color
      if (isset($fill_color[$i])) {
        $fill_color[$i] = array_map('intval', explode(',', $fill_color[$i]));
        $this->SetFillColor($fill_color[$i][0], $fill_color[$i][1], $fill_color[$i][2]);
        $fill_color_status = 1;
      } else {
        $fill_color_status = 0;
      }
      // Setting Font Style
      if (isset($font_style[$i]) || isset($font_weight[$i]) || isset($font_size[$i])) {
        $this->SetFont($font_style[$i], $font_weight[$i], $font_size[$i]);
      }
      $this->MultiCell($w, (int)@$this->heights, $data[$i], 0, $a, $fill_color_status, $m);
      //Put the position to the right of the cell
      $this->SetXY($x + $w, $y);
    }
    //Go to the next line
    $this->Ln($h);
  }
}
