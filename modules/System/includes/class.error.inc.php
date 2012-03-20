<?php 
// phpSysInfo - A PHP System Information Script
// http://phpsysinfo.sourceforge.net/
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// $Id: class.error.inc.php,v 1.4 2006/02/27 21:01:44 bigmichi1 Exp $

class Error {

  // Array which holds the error messages
  var $arrErrorList 	= array();
  // current number of errors encountered
  var $errors 		= 0;

  /**
  *
  *  addError()
  *  
  *  @param	strCommand	string		Command, which cause the Error
  *  @param	strMessage	string		additional Message, to describe the Error
  *  @param	intLine		integer		on which line the Error occours
  *  @param	strFile		string		in which File the Error occours
  *
  *  @return	-
  *
  **/
  function addError( $strCommand, $strMessage, $intLine, $strFile ) {
    $this->arrErrorList[$this->errors]['command'] = $strCommand;
    $this->arrErrorList[$this->errors]['message'] = $strMessage;
    $this->arrErrorList[$this->errors]['line']    = $intLine;
    $this->arrErrorList[$this->errors]['file']    = basename( $strFile );
    $this->errors++;
  }
  
  /**
  *
  * ErrorsAsHTML()
  *
  * @param	-
  *
  * @return	string		string which contains a HTML table which can be used to echo out the errors
  *
  **/
  function ErrorsAsHTML() {
    $strHTMLString = "";
    $strWARNString = "";
    $strHTMLhead = "<table width=\"100%\" border=\"0\">\n"
                 . " <tr>\n"
		 . "  <td><font size=\"-1\"><b>File</b></font></td>\n"
		 . "  <td><font size=\"-1\"><b>Line</b></font></td>\n"
		 . "  <td><font size=\"-1\"><b>Command</b></font></td>\n"
		 . "  <td><font size=\"-1\"><b>Message</b></font></td>\n"
		 . " </tr>\n";
    $strHTMLfoot = "</table>";
    
    if( $this->errors > 0 ) {
      foreach( $this->arrErrorList as $arrLine ) {
        if( $arrLine['command'] == "WARN" ) {
	  $strWARNString .= "<font size=\"-1\"><b>WARNING: " . htmlspecialchars( $arrLine['message'] ) . "</b></font><br/>\n";
	} else {
          $strHTMLString .= " <tr>\n"
                          . "  <td><font size=\"-1\">" . htmlspecialchars( $arrLine['file'] ) . "</font></td>\n"
                          . "  <td><font size=\"-1\">" . $arrLine['line'] . "</font></td>\n"
                          . "  <td><font size=\"-1\">" . htmlspecialchars( $arrLine['command'] ) . "</font></td>\n"
                          . "  <td><font size=\"-1\">" . htmlspecialchars( $arrLine['message'] ) . "</font></td>\n"
                          . " </tr>\n";
	}
      }
    }
    
    if( !empty( $strHTMLString ) ) {
      $strHTMLString = $strWARNString . $strHTMLhead . $strHTMLString . $strHTMLfoot;
    } else {
      $strHTMLString = $strWARNString;
    }
    
    return $strHTMLString;
  }

  /**
  *
  * ErrorsExist()
  *
  * @param	-
  *
  * @return 	true	there are errors logged
  *		false	no errors logged
  *
  **/
  function ErrorsExist() {
    if( $this->errors > 0 ) {
      return true;
    } else {
      return false;
    }
  }
}
?>
