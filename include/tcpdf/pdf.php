<?php
require('include/tcpdf/tcpdf.php');
// Xavier Nicolay 2004
// Version 1.01
class PDF extends TCPDF
{
// private variables
var $columns;
var $format;
var $angle=0;

// private functions
function RoundedRect($x, $y, $w, $h, $r, $style = '')
{
	$k = $this->k;
	$hp = $this->h;
	if($style=='F')
		$op='f';
	elseif($style=='FD' or $style=='DF')
		$op='B';
	else
		$op='S';
	$MyArc = 4/3 * (sqrt(2) - 1);
	$this->_out(sprintf('%.2f %.2f m',($x+$r)*$k,($hp-$y)*$k ));
	$xc = $x+$w-$r ;
	$yc = $y+$r;
	$this->_out(sprintf('%.2f %.2f l', $xc*$k,($hp-$y)*$k ));

	$this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
	$xc = $x+$w-$r ;
	$yc = $y+$h-$r;
	$this->_out(sprintf('%.2f %.2f l',($x+$w)*$k,($hp-$yc)*$k));
	$this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
	$xc = $x+$r ;
	$yc = $y+$h-$r;
	$this->_out(sprintf('%.2f %.2f l',$xc*$k,($hp-($y+$h))*$k));
	$this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
	$xc = $x+$r ;
	$yc = $y+$r;
	$this->_out(sprintf('%.2f %.2f l',($x)*$k,($hp-$yc)*$k ));
	$this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
	$this->_out($op);
}

function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
{
	$h = $this->h;
	$this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c ', $x1*$this->k, ($h-$y1)*$this->k,
						$x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
}

function Rotate($angle,$x=-1,$y=-1)
{
	if($x==-1)
		$x=$this->x;
	if($y==-1)
		$y=$this->y;
	if($this->angle!=0)
		$this->_out('Q');
	$this->angle=$angle;
	if($angle!=0)
	{
		$angle*=M_PI/180;
		$c=cos($angle);
		$s=sin($angle);
		$cx=$x*$this->k;
		$cy=($this->h-$y)*$this->k;
		$this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
	}
}

function _endpage()
{
	if($this->angle!=0)
	{
		$this->angle=0;
		$this->_out('Q');
	}
	parent::_endpage();
}

// public functions
function sizeOfText( $text, $largeur )
{
	$index    = 0;
	$nb_lines = 0;
	$loop     = TRUE;
	while ( $loop )
	{
		$pos = strpos($text, "\n");
		if (!$pos)
		{
			$loop  = FALSE;
			$line = $text;
		}
		else
		{
			$line  = substr( $text, $index, $pos);
			$text = substr( $text, $pos+1 );
		}
		$length = floor( $this->GetStringWidth( $line ) );
		$res = 1 + floor( $length / $largeur) ;
		$nb_lines += $res;
	}
	return $nb_lines;
}

// addImage
// Default will place vtiger in the top left corner
function addImage( $logo_name, $location=array('10','10','0','0') ) {
	if($logo_name)//error checking just in case, by OpenCRM
	{
		$x1 = $location[0];
		$y1 = $location[1];
		$stretchx = $location[2];
		$stretchy = $location[3];
		$this->Image('test/logo/'.$logo_name,$x1,$y1,$stretchx,$stretchy);
	}
}

// Company
function addCompany( $nom, $address, $location='' )
{
	$x1 = $location[0];
	$y1 = $location[1];
	//Positionnement en bas
	$this->SetXY( $x1, $y1 );
	$this->SetFont('Arial','B',12);
	$length = $this->GetStringWidth( $nom );
	$this->Cell( $length, 2, $nom);
	$this->SetXY( $x1, $y1 + 4 );
	$this->SetFont('Arial','',10);
	$length = $this->GetStringWidth( $address );
	//Coordonnées de la société
	$lines = $this->sizeOfText( $address, $length) ;
	$this->MultiCell($length, 4, $address);
}

// bubble blocks
function title ($label, $total, $position)
{
	$r1  = $position[0];
	$r2  = $r1 + 19 + $position[2] ;
	$y1  = $position[1];
	$y2  = $y1;
	$mid = $y1 + ($y2 / 2);
	$width=10;
	$this->SetFillColor(192);
	$this->RoundedRect($r1-16, $y1-1, 52, $y2+1, 2.5, 'DF');
	$this->SetXY( $r1 + 4, $y1+1 );
	$this->SetFont( "Helvetica", "B", 15);
	$this->Cell($width,5, $label." ".$total, 0, 0, "C");
}

// text block, non-wrapped
function addTextBlock( $title,$text,$positions )
{
	$r1  = $positions[0];
	$y1  = $positions[1];
	$this->SetXY( $r1, $y1);
	$this->SetFont( "Helvetica", "B", 10);
	$this->Cell( $positions[2], 4,$title);
	$this->SetXY( $r1, $y1+4);
	$this->SetFont( "Helvetica", "", 10);
	$this->MultiCell( $positions[2], 4, $text);
}

function tableWrapper($position)
{
	$r1  = $position[0];
	$r2  = $r1 + 19 + $position[2] ;
	$y1  = $position[1];
	if($position[3])
		$y2  = $position[3];
	else
		$y2  = 17;

	$mid = $y1 + (13 / 2);
	$width=10;
	$this->RoundedRect($r1, $y1, ($r2 - $r1), $y2, 4.5, 'D');
	$this->Line( $r1, $mid, $r2, $mid);
	$this->SetXY( $r1 + ($r2-$r1)/2 - 3, $y1+3 );
	$this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1 + 9 );
}

function addBubble($page,$title,$position)
{
	$r1  = $position[0];
	$r2  = $r1 + 19 + $position[2] ;
	$y1  = $position[1];
	if($position[3])
		$y2  = 17*$position[3];
	else
		$y2  = 17;

	$mid = $y1 + (19 / 2);
	$width=10;
	$this->RoundedRect($r1, $y1, ($r2 - $r1), $y2, 4.5, 'D');
	$this->Line( $r1, $mid, $r2, $mid);
	$this->SetXY( $r1 + ($r2-$r1)/2 - 3, $y1+3 );
	$this->SetFont( "Helvetica", "B", 10);
	$this->Cell($width,5, $title, 0, 0, "C");
	$this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1 + 9 );
	$this->SetFont( "Helvetica", "", 10);
	$this->MultiCell($width,5,$page, 0,0, "C");
}

// bubble blocks
function addBubbleBlock ($page, $title, $position)
{
	$r1  = $position[0];
	$r2  = $r1 + 19 + $position[2] ;
	$y1  = $position[1];
	$y2  = 17;

	$mid = $y1 + ($y2 / 2);
	$width=10;
	$this->RoundedRect($r1, $y1, ($r2 - $r1), $y2, 4.5, 'D');
	$this->Line( $r1, $mid, $r2, $mid);
	$this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1+3 );
	$this->SetFont( "Helvetica", "B", 10);
	$this->Cell($width,5, $title, 0, 0, "C");
	$this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1 + 9 );
	$this->SetFont( "Helvetica", "", 10);
	$this->Cell($width,5,$page, 0,0, "C");
}

// record blocks
function addRecBlock( $data, $title, $postion )
{
	$lengthtitle = strlen($title);
	$lengthdata = strlen($data);
	$length=$lengthtitle;
	$r1  = $postion[0];
	$r2  = $r1 + 40 + $length;
	$y1  = $postion[1];
	$y2  = $y1+10;
	$mid = $y1 + (($y2-$y1) / 2);

	$this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'D');
	$this->Line( $r1, $mid, $r2, $mid);
	$this->SetXY( $r1 + ($r2-$r1)/2 -5 , $y1+1 );
	$this->SetFont( "Helvetica", "B", 10);
	$this->Cell(10,4, $title, 0, 0, "C");
	$this->SetXY( $r1 + ($r2-$r1)/2 -5 , $y1 + 5 );
	$this->SetFont( "Helvetica", "", 10);
	$this->Cell(10,4,$data, 0, 0, "C");
}

// description blocks
function addDescBlock( $data, $title, $position )
{
	$lengthtitle = strlen($title);
	$lengthdata= $position[3];

	$length=$position[2];
	$r1  = $position[0];
	$r2  = $r1 + 40 + $length;
	$y1  = $position[1];
	$y2  = $y1+10;
	$mid = $y1 + (($y2-$y1) / 2);

	$this->RoundedRect($r1,$y1, ($length + 40), ($lengthdata/140*30), 2.5, 'D');
	$this->Line( $r1, $mid, $r2, $mid);

	$this->SetXY( $position[0]+2 , $y1 + 1 );
	$this->SetFont( "Helvetica", "B", 10);
	$this->Cell(10,4, $title);

	$this->SetXY( $position[0]+2 , $y1 + 6 );
	$this->SetFont( "Helvetica", "", 10);
	$this->MultiCell(($length+36),4,$data);
}

function drawLine($positions)
{
	$x=$positions[0];
	$y=$positions[1];
	$width=$positions[2];
	$this->Line( $x, $y, $x+$width, $y);
}

// add columns to table
function addCols( $tab ,$positions ,$bottom, $taxtype = 'group')
{
	global $columns,$app_strings;

	$r1  = 10;
	$r2  = $this->w - ($r1 * 2) ;
	$y1  = 80;
	$x1  = $positions[1];
	$y2  = $bottom;
	$this->SetXY( $r1, $y1 );
	$this->SetFont( "Helvetica", "", 10);

	$colX = $r1;
	$columns = $tab;
	while ( list( $lib, $pos ) = each ($tab) )
	{
		$this->SetXY( $colX, $y1+3 );
		$this->Cell( $pos, 1, $app_strings[$lib], 0, 0, "C");
		$colX += $pos;
		switch($lib) {
	  		case 'Total':
			break;
	  		case 'Qty':
	  		case 'Price':
				if($taxtype == "individual")
					$this->Line( $colX, $y1, $colX, (($y1+$y2)-37));
				else
					$this->Line( $colX, $y1, $colX, (($y1+$y2)-43));
			break;
	  		default:
				if($taxtype == "individual" && $lib == 'Discount')
					$this->Line( $colX, $y1, $colX, (($y1+$y2)-37));
				else
					$this->Line( $colX, $y1, $colX, ($y1+$y2));
	  		break;
		}
	}
}

function addLineFormat( $tab )
{
	global $format, $columns;

	while ( list( $lib, $pos ) = each ($columns) )
	{
		if ( isset( $tab["$lib"] ) )
			$format[ $lib ] = $tab["$lib"];
	}
}

function addProductLine( $line, $tab, $totals='' )
{
	global $columns, $format;

	$ordonnee     = 10;
	$maxSize      = $line;

	reset( $columns );
	while ( list( $lib, $pos ) = each ($columns) )
	{
		$longCell = $pos -2;
		$text    = $tab[ $lib ];
		$length    = $this->GetStringWidth( $text );
		$formText  = $format[ $lib ];
		$this->SetXY( $ordonnee, $line);
		$this->MultiCell( $longCell, 3 , $text, 3, $formText);
		if ( $maxSize < ($this->GetY()  ) )
			$maxSize = $this->GetY() ;
		$ordonnee += $pos;
	}
	return ( $maxSize - $line );
}

function addTotalsRec($names, $totals, $positions)
{
	$this->SetFont( "Arial", "B", 8);
	$r1  = $positions[0];
	$r2  = $r1 + 90;
	$y1  = $positions[1];
	$y2  = $y1+10;
	$this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'D');
	$this->Line( $r1, $y1+4, $r2, $y1+4);
	$this->Line( $r1+27, $y1, $r1+27, $y2);  // avant Subtotal
	$this->Line( $r1+43, $y1, $r1+43, $y2);  // avant Tax
	$this->Line( $r1+66, $y1, $r1+66, $y2);  // avant Adjustment

	$this->SetXY( $r1+2, $y1);
	$this->Cell(10,4, $names[0]);
	$this->SetX( $r1+29,$y1 );
	$this->Cell(10,4, $names[1]);
	$this->SetX( $r1+45 );
	$this->Cell(10,4, $names[2]);
	$this->SetX( $r1+66 );
	$this->Cell(10,4, $names[3]);


	$this->SetXY( $r1+2, $y1+5 );
	$this->Cell( 10,4, $totals[0] );
	$this->SetXY( $r1+29, $y1+5 );
	$this->Cell( 10,4, $totals[1] );
	$this->SetXY( $r1+44, $y1+5 );
	$this->Cell( 10,4, $totals[2] );
	$this->SetXY( $r1+66, $y1+5 );
	$this->Cell( 10,4, $totals[3] );

	$this->SetFont( "Arial", "B", 6);
	$this->SetXY( $r1+90, $y2 - 8 );
	$this->SetFont( "Helvetica", "", 10);
}

// add a watermark (temporary estimate, DUPLICATA...)
// call this method first
function watermark( $text, $positions, $rotate = array('45','50','180') )
{
	$this->SetFont('Arial','B',50);
	$this->SetTextColor(230,230,230);
	$this->Rotate($rotate[0],$rotate[1],$rotate[2]);
	$this->Text($positions[0],$positions[1],$text);
	$this->Rotate(0);
	$this->SetTextColor(0,0,0);
}

}
function StripLastZero($string)
{
	$count=strlen($string);
	$ret=substr($string,0,($count-1));
	return $ret;
}
?>
