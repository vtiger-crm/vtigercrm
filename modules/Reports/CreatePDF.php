<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
ini_set('max_execution_time','1800');
require_once("modules/Reports/ReportRun.php");
require_once("modules/Reports/Reports.php");
//require('include/fpdf/fpdf.php');
require('include/tcpdf/tcpdf.php');
$language = $_SESSION['authenticated_user_language'].'.lang.php';
require_once("include/language/$language");
//a hex html code (e.g. #3FE5AA)
/*function hex2dec($couleur = "#000000"){
    $R = substr($couleur, 1, 2);
    $rouge = hexdec($R);
    $V = substr($couleur, 3, 2);
    $vert = hexdec($V);
    $B = substr($couleur, 5, 2);
    $bleu = hexdec($B);
    $tbl_couleur = array();
    $tbl_couleur['R']=$rouge;
    $tbl_couleur['G']=$vert;
    $tbl_couleur['B']=$bleu;
    return $tbl_couleur;
}

//conversion pixel -> millimeter in 72 dpi
function px2mm($px){
    return $px*25.4/72;
}

function txtentities($html){
    $trans = get_html_translation_table(HTML_ENTITIES);
    $trans = array_flip($trans);
    return strtr($html, $trans);
}
////////////////////////////////////

class Html2PDF extends FPDF
{
//variables of html parser
var $B;
var $I;
var $U;
var $HREF;
var $fontList;
var $issetfont;
var $issetcolor;

function Html2PDF($orientation='P',$unit='mm',$format='A4')
{
//echo '<pre>';print_r($format);echo '</pre>';	
    //Call parent constructor
    $this->FPDF($orientation,$unit,$format);
    //Initialization
    $this->B=0;
    $this->I=0;
    $this->U=0;
    $this->HREF='';

    $this->tableborder=0;
    $this->tdbegin=false;
    $this->tdwidth=0;
    $this->tdheight=0;
    $this->tdalign="L";
    $this->tdbgcolor=false;

    $this->oldx=0;
    $this->oldy=0;

    $this->fontlist=array("arial","times","courier","helvetica","symbol");
    $this->issetfont=false;
    $this->issetcolor=false;
}

//////////////////////////////////////
//html parser

function WriteHTML($html)
{
    $html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote><hr><td><tr><table><sup>"); //remove all unsupported tags
    $html=str_replace("\n",'',$html); //replace carriage returns by spaces
    $html=str_replace("\t",'',$html); //replace carriage returns by spaces
    $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //explodes the string
    foreach($a as $i=>$e)
    {
        if($i%2==0)
        {
            //Text
            if($this->HREF)
                $this->PutLink($this->HREF,$e);
            elseif($this->tdbegin) {
                if(trim($e)!='' and $e!="&nbsp;") {
                    $this->Cell($this->tdwidth,$this->tdheight,$e,$this->tableborder,'',$this->tdalign,$this->tdbgcolor);
                }
                elseif($e=="&nbsp;") {
                    $this->Cell($this->tdwidth,$this->tdheight,'',$this->tableborder,'',$this->tdalign,$this->tdbgcolor);
                }
            }
            else
                $this->Write(5,stripslashes(txtentities($e)));
        }
        else
        {
            //Tag
            if($e{0}=='/')
                $this->CloseTag(strtoupper(substr($e,1)));
            else
            {
                //Extract attributes
                $a2=explode(' ',$e);
                $tag=strtoupper(array_shift($a2));
                $attr=array();
                foreach($a2 as $v)
                    if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
                        $attr[strtoupper($a3[1])]=$a3[2];
                $this->OpenTag($tag,$attr);
            }
        }
    }
}

function OpenTag($tag,$attr)
{
    //Opening tag
    switch($tag){

        case 'SUP':
            if($attr['SUP'] != '') {    
                //Set current font to: Bold, 6pt     
                $this->SetFont('','',6);
                //Start 125cm plus width of cell to the right of left margin         
                //Superscript "1"
                $this->Cell(2,2,$attr['SUP'],0,0,'L');
            }
            break;

        case 'TABLE': // TABLE-BEGIN
            if( $attr['BORDER'] != '' ) $this->tableborder=$attr['BORDER'];
            else $this->tableborder=0;
            break;
        case 'TR': //TR-BEGIN
            break;
        case 'TD': // TD-BEGIN
            if( $attr['WIDTH'] != '' ) $this->tdwidth=($attr['WIDTH']/4);
            else $this->tdwidth=40; // SET to your own widt if you need bigger fixed cells
            if( $attr['HEIGHT'] != '') $this->tdheight=($attr['HEIGHT']/6);
            else $this->tdheight=6; // SET to your own height if you need bigger fixed cells
            if( $attr['ALIGN'] != '' ) {
                $align=$attr['ALIGN'];        
                if($align=="LEFT") $this->tdalign="L";
                if($align=="CENTER") $this->tdalign="C";
                if($align=="RIGHT") $this->tdalign="R";
            }
            else $this->tdalign="L"; // SET to your own
            if( $attr['BGCOLOR'] != '' ) {
                $coul=hex2dec($attr['BGCOLOR']);
                    $this->SetFillColor($coul['R'],$coul['G'],$coul['B']);
                    $this->tdbgcolor=true;
                }
            $this->tdbegin=true;
            break;

        case 'HR':
            if( $attr['WIDTH'] != '' )
                $Width = $attr['WIDTH'];
            else
                $Width = $this->w - $this->lMargin-$this->rMargin;
            $x = $this->GetX();
            $y = $this->GetY();
            $this->SetLineWidth(0.2);
            $this->Line($x,$y,$x+$Width,$y);
            $this->SetLineWidth(0.2);
            $this->Ln(1);
            break;
        case 'STRONG':
            $this->SetStyle('B',true);
            break;
        case 'EM':
            $this->SetStyle('I',true);
            break;
        case 'B':
        case 'I':
        case 'U':
            $this->SetStyle($tag,true);
            break;
        case 'A':
            $this->HREF=$attr['HREF'];
            break;
        case 'IMG':
            if(isset($attr['SRC']) and (isset($attr['WIDTH']) or isset($attr['HEIGHT']))) {
                if(!isset($attr['WIDTH']))
                    $attr['WIDTH'] = 0;
                if(!isset($attr['HEIGHT']))
                    $attr['HEIGHT'] = 0;
                $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
            }
            break;
        //case 'TR':
        case 'BLOCKQUOTE':
        case 'BR':
            $this->Ln(5);
            break;
        case 'P':
            $this->Ln(10);
            break;
        case 'FONT':
            if (isset($attr['COLOR']) and $attr['COLOR']!='') {
                $coul=hex2dec($attr['COLOR']);
                $this->SetTextColor($coul['R'],$coul['G'],$coul['B']);
                $this->issetcolor=true;
            }
            if (isset($attr['FACE']) and in_array(strtolower($attr['FACE']), $this->fontlist)) {
                $this->SetFont(strtolower($attr['FACE']));
                $this->issetfont=true;
            }
            if (isset($attr['FACE']) and in_array(strtolower($attr['FACE']), $this->fontlist) and isset($attr['SIZE']) and $attr['SIZE']!='') {
                $this->SetFont(strtolower($attr['FACE']),'',$attr['SIZE']);
                $this->issetfont=true;
            }
            break;
    }
}

function CloseTag($tag)
{
    //Closing tag
    if($tag=='SUP') {
    }

    if($tag=='TD') { // TD-END
        $this->tdbegin=false;
        $this->tdwidth=0;
        $this->tdheight=0;
        $this->tdalign="L";
        $this->tdbgcolor=false;
    }
    if($tag=='TR') { // TR-END
        $this->Ln();
    }
    if($tag=='TABLE') { // TABLE-END
        $this->tableborder=0;
    }

    if($tag=='STRONG')
        $tag='B';
    if($tag=='EM')
        $tag='I';
    if($tag=='B' or $tag=='I' or $tag=='U')
        $this->SetStyle($tag,false);
    if($tag=='A')
        $this->HREF='';
    if($tag=='FONT'){
        if ($this->issetcolor==true) {
            $this->SetTextColor(0);
        }
        if ($this->issetfont) {
            $this->SetFont('arial');
            $this->issetfont=false;
        }
    }
}

function SetStyle($tag,$enable)
{
    //Modify style and select corresponding font
    $this->$tag+=($enable ? 1 : -1);
    $style='';
    foreach(array('B','I','U') as $s)
        if($this->$s>0)
            $style.=$s;
    $this->SetFont('',$style);
}

function PutLink($URL,$txt)
{
    //Put a hyperlink
    $this->SetTextColor(0,0,255);
    $this->SetStyle('U',true);
    $this->Write(5,$txt,$URL);
    $this->SetStyle('U',false);
    $this->SetTextColor(0);
}

}//end of class
*/
$reportid = vtlib_purify($_REQUEST["record"]);
$oReport = new Reports($reportid);
//Code given by Csar Rodrguez for Rwport Filter
$filtercolumn = $_REQUEST["stdDateFilterField"];
$filter = $_REQUEST["stdDateFilter"];
$oReportRun = new ReportRun($reportid);

$startdate = getDBInsertDateValue($_REQUEST["startdate"]);//Convert the user date format to DB date format 
$enddate = getDBInsertDateValue($_REQUEST["enddate"]);//Convert the user date format to DB date format
$filterlist = $oReportRun->RunTimeFilter($filtercolumn,$filter,$startdate,$enddate);
$arr_val = $oReportRun->GenerateReport("PDF",$filterlist);

if(isset($arr_val))
{
	
	foreach($arr_val as $wkey=>$warray_value)
        {
                foreach($warray_value as $whd=>$wvalue)
                {
			if(strlen($wvalue) < strlen($whd))
			{
				$w_inner_array[] = strlen($whd);	
			}else
			{
				$w_inner_array[] = strlen($wvalue);
			}
                }
		$warr_val[] = $w_inner_array;
		unset($w_inner_array);
        }

	foreach($warr_val[0] as $fkey=>$fvalue)
	{
		foreach($warr_val as $wkey=>$wvalue)
		{
			
			$f_inner_array[] = $warr_val[$wkey][$fkey];
		}
		sort($f_inner_array,1);
		$farr_val[] = $f_inner_array;
		unset($f_inner_array);
	}
	
	foreach($farr_val as $skkey=>$skvalue)
	{
		if($skvalue[count($arr_val)-1] == 1)
		{
			$col_width[] = ($skvalue[count($arr_val)-1] * 100);
		} else 
		{
			$col_width[] = ($skvalue[count($arr_val)-1] * 10) + 10 ;	
		}
	}
	$count = 0;
	foreach($arr_val[0] as $key=>$value)
	{
		$headerHTML .= '<td width="'.$col_width[$count].'" bgcolor="#DDDDDD"><b>'.$oReportRun->getLstringforReportHeaders($key).'</b></td>';
		$count = $count + 1;
	}
	
	foreach($arr_val as $key=>$array_value)
	{
		$valueHTML = "";
		$count = 0;
		foreach($array_value as $hd=>$value)
		{
			$valueHTML .= '<td width="'.$col_width[$count].'">'.$value.'</td>';
			$count = $count + 1;
		}
		$dataHTML .= '<tr>'.$valueHTML.'</tr>';
	}

}

$totalpdf = $oReportRun->GenerateReport("PRINT_TOTAL",$filterlist);
$html = '<table border="1"><tr>'.$headerHTML.'</tr>'.$dataHTML.'<tr><td>'.$totalpdf.'</td></tr>'.'</table>';
$columnlength = array_sum($col_width);
if($columnlength > 14400)
{
	die("<br><br><center>".$app_strings['LBL_PDF']." <a href='javascript:window.history.back()'>".$app_strings['LBL_GO_BACK'].".</a></center>");
}
if($columnlength <= 420 )
{
	$pdf = new TCPDF('P','mm','A5',true);
	
}elseif($columnlength >= 421 && $columnlength <= 1120)
{
	$pdf = new TCPDF('L','mm','A3',true);

}elseif($columnlength >=1121 && $columnlength <= 1600)
{
	$pdf = new TCPDF('L','mm','A2',true);

}elseif($columnlength >=1601 && $columnlength <= 2200)
{
	$pdf = new TCPDF('L','mm','A1',true);
}
elseif($columnlength >=2201 && $columnlength <= 3370)
{
	$pdf = new TCPDF('L','mm','A0',true);
}
elseif($columnlength >=3371 && $columnlength <= 4690)
{
	$pdf = new TCPDF('L','mm','2A0',true);
}
elseif($columnlength >=4691 && $columnlength <= 6490)
{
	$pdf = new TCPDF('L','mm','4A0',true);
}
else
{
	$columnhight = count($arr_val)*15;
	$format = array($columnhight,$columnlength);
	$pdf = new TCPDF('L','mm',$format,true);
}
$pdf->SetMargins(10, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->setLanguageArray($l);
//echo '<pre>';print_r($columnlength);echo '</pre>';
$pdf->AddPage();

$pdf->SetFillColor(224,235,255);
$pdf->SetTextColor(0);
$pdf->SetFont('FreeSerif','B',14);
$pdf->Cell(($pdf->columnlength*50),10,getTranslatedString($oReport->reportname),0,0,'C',0);
//$pdf->writeHTML($oReport->reportname);
$pdf->Ln();

$pdf->SetFont('FreeSerif','',10);

$pdf->writeHTML($html);
$pdf->Output('Reports.pdf','D');
exit();
?>
