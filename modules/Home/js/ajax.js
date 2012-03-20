/*
    Copyright 2005 Rolando Gonzalez (rolosworld@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function getRequester()
{
  try
    {
      if(window.XMLHttpRequest) 
        {
          return new XMLHttpRequest();
        } 
      else if(window.ActiveXObject)
        {
          return new ActiveXObject("Microsoft.XMLHTTP");
        }
    }
  catch (e) 
    {
      alert("You need a browser which supports an XMLHttpRequest Object.\nMozilla build 0.9.5 has this Object and IE5 and above.");
    }
};

/*
onreadystatechange	Event handler for an event that fires at every state change
readyState	Object status integer:
0 = uninitialized
1 = loading
2 = loaded
3 = interactive
4 = complete
responseText	String version of data returned from server process
responseXML	DOM-compatible document object of data returned from server process
status	Numeric code returned by server, such as 404 for "Not Found" or 200 for "OK"
statusText	String message accompanying the status code
*/
function Ajax(cb)
{
  var me = this;
  this.requester = getRequester();
  
  if(cb)
    this.callback = cb;
  else
    this.callback = function(req)
    {
      return eval(req.responseText);
    };

  this.requester.onreadystatechange = function(){
    switch(me.requester.readyState)
    {
      case 1:
      case 2:
      case 3:
        break;
      case 4:
        var response = me.callback(me.requester);
        break;
      default:
        alert("Error");
        break;
    }
  };

  this.state = function()
  {
    return me.requester.readyState;
  };

  this.process = function(url, parameters){
    me.requester.open("POST", url, true);
    me.requester.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    me.requester.setRequestHeader("Content-length", parameters.length);
    //me.requester.setRequestHeader("Connection", "close");
    me.requester.send(parameters);
  };
};
