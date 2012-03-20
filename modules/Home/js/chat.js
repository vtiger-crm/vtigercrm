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

var chats = new Array(); // private chats opened
var users = new Array(); // users list
var chat_data = null;
var mlid = 0; //msg last id
var DEBUG = false;
var PRIVATES = true;
var PUBLIC = true;

function getNick(uid)
{
  for(var i in users)
  {
    if(users[i].uid == uid)
      return users[i].nick;
  }
  return null;
};

function resetChatsZ()
{
  for(var i = 0; i < chats.length; i++)
    if(chats[i])
      chats[i].win.table.style.zIndex = chats[i].win.z;
};

function debug(dib,msg)
{
  if(!DEBUG) return;
  if(document.getElementById(dib))
    document.getElementById(dib).innerHTML = msg;
  else
    alert(msg);
};

// initialize chat connection.
function Chat(conf)
{
  var me = this;
  this.pcid = conf["pchatid"]?conf["pchatid"]:null;
  this.dt = conf["dt"]?conf["dt"]:1000;
  this.ulid = conf["ulid"]?conf["ulid"]:null;
  this.w = conf["width"]?conf["width"]:"400px";
  this.h = conf["height"]?conf["height"]:"300px";
  this.ulist = null;

  if(this.pcid)
  {
    chats[0] = new PubChat(this.dt,this.w,this.h);
    document.getElementById(this.pcid).appendChild(chats[0].get());
  }

  // evaluate the returned json
  this.callback = function(response)
  {
    me.ajax = null;
    try{
      me.doRefresh(response.responseText);
    }catch(e){
      debug("debug","Chat Error: "+response.responseText);
      clearInterval(me.interv);
      debug("debug",e.toString());
      debug("debug",e.filename+":"+e.lineNumber);
      return;
    }
    me.ajax = new Ajax(me.callback);
    debug("debug",response.responseText);
    window.status=Date();
  };

  this.doRefresh = function(response)
  {
    if(!me.ulist)
      me.ulist = new UList(me.dt,me.ulid,me.w,me.h);
    chat_data = eval("("+response+")");
    me.ulist.refresh();
    me.refreshChats();
  };

  this.ajax = new Ajax(me.callback);

  // start the ajax request for the chat data
  this.refresh = function()
  {
    if(me.ajax.state() == 0)
      me.ajax.process("index.php?mode=chat&module=Home&action=chat","submode=get_all&mlid="+mlid);
  };
  this.interv = setInterval(me.refresh,me.dt);

  this.refreshChats = function()
  {
    if(chat_data.pvchat)
    {
      cnum = 0;
      for(var i in chat_data.pvchat)
      {
        cnum = chat_data.pvchat[i].chat;
        if(!chats[cnum])
          chats[cnum] = new PrivChat(me.dt,cnum,me.w,me.h);

        if(mlid < chat_data.pvchat[i].mlid)
          mlid = chat_data.pvchat[i].mlid;

        if(chat_data.pvchat[i].msg.substr(0,5) == "\\sys ")
        {
          chats[cnum].appendSysMsg(chat_data.pvchat[i].msg.substr(5));
        }
        else
          chats[cnum].appendMsg(chat_data.pvchat[i].from,chat_data.pvchat[i].msg);
      }
      chat_data.pvchat = null;
    }

    if(PUBLIC && chat_data.pchat)
    {
      for(var i in chat_data.pchat)
      {
        if(mlid < chat_data.pchat[i].mlid)
          mlid = chat_data.pchat[i].mlid;

        if(!chats[0])
          continue;

        if(chat_data.pchat[i].msg.substr(0,5) == "\\sys ")
        {
          chats[0].appendSysMsg(chat_data.pchat[i].msg.substr(5));
        }
        else
          chats[0].appendMsg(chat_data.pchat[i].from,chat_data.pchat[i].msg);
      }
      chat_data.pchat = null;
    }
  };
};

// User list handler
function UList(dt,ulid,w,h)
{
  var me = this;
  this.w = w;
  this.h = h;
  this.dt = dt; // delta time to sleep the requests.
  this.ulid = ulid; // user list ul tag id.
  this.interv = null;

  // updates the users list on the html
  this.refreshList = function()
  {
    if(!document.getElementById(me.ulid))
    {
      clearInterval(me.interv);
      return;
    }

    while(document.getElementById(me.ulid).firstChild)
      document.getElementById(me.ulid).removeChild(document.getElementById(me.ulid).firstChild);

    if(users)
    {
      var li;
      var a;
      var user;
      for(var i in users)
      {
        user = users[i];
        li = document.createElement("span");
		a = document.createElement("a");
        a.appendChild(document.createTextNode(user.nick));
        a.setAttribute("href","#");
        a.className = "chat";
        if(PRIVATES)
          a.onclick = me.newPriv(me.dt,user.uid);

        li.appendChild(a);
        document.getElementById(me.ulid).appendChild(li);
      }
      //var date = new Date();
      //window.status=date.toString();
    }
  };

  // higher order stuff
  this.newPriv = function(dt,uid)
  {
    return function()
    {
      if(!chats[uid])
      {
        resetChatsZ();
        chats[uid] = new PrivChat(dt,uid,me.w,me.h);
      }
      return false;
    };
  };

  // check if theres a new user list
  this.refresh = function()
  {
    //users = null;
    if(chat_data.ulist)
    {
      users = chat_data.ulist;
      me.refreshList();
    }
    chat_data.ulist = null;
  };
};

//////////////////////////////////////////////////////////
// Input handler
function chatInput(to)
{
  var me = this;
  this.to = to?to:null;
  this.input = document.createElement("input");
  this.input.className = "cinput";
  this.input.setAttribute("type","text");
  this.input.setAttribute("name","input");

  var table = document.createElement("table");
  //table.border = "1";
  var tbody = table.appendChild(document.createElement("tbody"));
  var tr = tbody.appendChild(document.createElement("tr"));
  var td = tr.appendChild(document.createElement("td"));
  var td1 = tr.appendChild(document.createElement("td"));
  var td2 = tr.appendChild(document.createElement("td"));

  table.className = "cinput";
  tbody.className = "cinput";
  tr.className = "cinput";
  td.className = "ckeyb";
  td1.className = "cinput";
  td2.className = "csubmit";

  this.input = td1.appendChild(this.input);
  td2.onclick = function()
  {
    var ajax = new Ajax(me.callback);
    ajax.process("index.php?mode=chat&module=Home&action=chat","submode=submit&msg="+escapeAll(me.input.value)+(me.to?"&to="+me.to:""));

    me.input.value = "";
    me.input.focus();
    return false;
  };

  this.form = document.createElement("form");
  this.form.className = "cinput";

  this.form.appendChild(table);
  this.form.onsubmit = function()
  {
    var ajax = new Ajax(me.callback);
    ajax.process("index.php?mode=chat&module=Home&action=chat","submode=submit&msg="+escapeAll(me.input.value)+(me.to?"&to="+me.to:""));
    me.input.value = "";
    me.input.focus();
    return false;
  };

  this.setFocus = function()
  {
    me.input.focus();
  };

  // evaluate the returned json
  this.callback = function(response)
  {
    response = response.responseText;
    try{
      if(response)
        debug("debug",response);
    }catch(e){
      debug("debug","chatInput Error: "+response);
    }
  };

  this.get = function()
  {
    return me.form;
  };
};

//////////////////////////////////////////////////////////
// Private chat handler / abre ventana de usurio + usuario
function PrivChat(dt,to,w,h)
{
  var me = this;
  this.dt = dt; // delta time to sleep the requests.
  this.to = to; // private chat the other user id
  this.input = new chatInput(to);
  this.toNick = getNick(to);

  var conf = new Array();
  conf["topic"] = "Private chat with <span class=\"chatTopicNick\">"+me.toNick+"</span>";
  conf["class"] = "chat";
  conf["width"] = w;
  conf["height"] = h;
  conf["drag"] = true;

  this.win = new cssWindow(conf);

  // move z+1..crappy way to send above others
  this.win.table.onDragStart = function()
  {
    resetChatsZ();
    chats[me.to].win.table.style.zIndex++;
  };

  this.win.cb = function()
  {
    me.ajax = new Ajax(me.callback);
    me.ajax.process("index.php?mode=chat&module=Home&action=chat","submode=pvclose&to="+me.to);
    chats[me.to]=null;
  };

  this.cbox = this.win.setBody(document.createElement("div"));
  this.cbox.style.width = "100%";
  this.cbox.style.height = "100%";
  this.cbox.style.overflow = "auto";
  this.cbox.className = "chatbox";

  // Draws the top of the window
  this.getHead = function()
  {
    var t = document.createElement("table");
    t.style.width="100%";t.cellSpacing="0";t.cellPadding="0";
    var tb = t.appendChild(document.createElement("tbody"));
    var tr = tb.appendChild(document.createElement("tr"));
    var td = tr.appendChild(document.createElement("td"));
    td.className = "chaticon";
    td = tr.appendChild(document.createElement("td"));
    td.className = "chattopic1";
    td.innerHTML = me.win.topic;
    var hide = tr.appendChild(document.createElement("td"));
    hide.className = "chathide";
    hide.onclick = me.win.doHide;
    var close = tr.appendChild(document.createElement("td"));
    close.className = "chatclose";
    close.onclick = me.win.doClose;
    return t;
  };

  this.win.setHead(this.getHead());
  this.win.setFoot(this.input.get());


  // updates the chat on the html
  this.appendMsg = function(from,msg)
  {
    var div;
    var span;

    span = document.createElement("span");
    span.appendChild(document.createTextNode(from+": "));
    span.className = "cunick";

    div = document.createElement("div");
    div.className = "cumsg";
        
    div.appendChild(span);
    div.innerHTML+=msg;
    me.cbox.appendChild(div);
    me.cbox.scrollTop=me.cbox.scrollHeight;
  };

  // updates the chat on the html
  this.appendSysMsg = function(msg)
  {
    var div;
    div = document.createElement("div");
    div.className = "csmsg";
    div.innerHTML+=msg;
    me.cbox.appendChild(div);
    me.cbox.scrollTop=me.cbox.scrollHeight;
  };

  this.win.show();
  this.input.setFocus();
};

//////////////////////////////////////////////////////////
// Public chat handler / abre ventana de usurio + usuario
function PubChat(dt,w,h)
{
  var me = this;
  this.dt = dt; // delta time to sleep the requests.
  this.to = 0; // public chat the other user id
  this.input = new chatInput(this.to);

  var conf = new Array();
  conf["topic"] = "Public Chat";
  conf["class"] = "pchat";
  conf["width"] = w;
  conf["height"] = h;
  conf["drag"] = false;

  this.win = new cssWindow(conf);

  this.cbox = this.win.setBody(document.createElement("div"));
  this.cbox.style.width = "100%";
  this.cbox.style.height = "100%";
  this.cbox.style.overflow = "auto";
  this.cbox.className = "chatbox";

  // Draws the top of the window
  this.getHead = function()
  {
    var t = document.createElement("table");
    t.style.width="100%";t.cellSpacing="0";t.cellPadding="0";
    var tb = t.appendChild(document.createElement("tbody"));
    var tr = tb.appendChild(document.createElement("tr"));
    var td = tr.appendChild(document.createElement("td"));
    td.className = "chattopic";
    td.appendChild(document.createTextNode(me.win.topic));
    var hide = tr.appendChild(document.createElement("td"));
    hide.className = "chathide";
    hide.onclick = me.win.doHide;

    return t;
  };

  this.win.setHead(this.getHead());
  this.win.setFoot(this.input.get());

  // updates the chat on the html
  this.appendMsg = function(from,msg)
  {
    var div;
    var span;

    span = document.createElement("span");
    span.appendChild(document.createTextNode(from+": "));
    span.className = "cunick";

    div = document.createElement("div");
    div.className = "cumsg";
        
    div.appendChild(span);
    div.innerHTML+=msg;
    me.cbox.appendChild(div);
    me.cbox.scrollTop=me.cbox.scrollHeight;
  };

  // updates the chat on the html
  this.appendSysMsg = function(msg)
  {
    var div;
    div = document.createElement("div");
    div.className = "csmsg";
    div.innerHTML+=msg;
    me.cbox.appendChild(div);
    me.cbox.scrollTop=me.cbox.scrollHeight;
  };

  this.get = function()
  {
    me.input.setFocus();
    return me.win.get();
  };
};
