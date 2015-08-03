/* API.js */

var API = function()
{

};

API.prototype.registerUser = function (username, password, invite_code, public_key, private_key, salt, callback)
{
	var data = {};
	data["username"] = username;
	data["password"] = password;
	data["invite_code"] = invite_code;
	data["public_key"] = public_key;
	data["private_key"] = private_key;
    data["salt"] = salt;

    return this.sendRequest('post', 'api/register', data, callback);
};

API.prototype.loginUser = function (username, password, callback)
{
    var data = {};
    data["username"] = username;
    data["password"] = password;

    return this.sendRequest('post', 'api/login', data, callback);
};


API.prototype.getNewSalt = function (callback)
{
    return this.sendRequest('get', 'api/newsalt', "", callback);
};


API.prototype.getSaltForUser = function (username, callback)
{
    var data = {};
    data["username"] = username;
    return this.sendRequest('get', 'api/salt', data, callback);
};


API.prototype.getUsernames = function (token, callback)
{
    this.sendAuthRequest('get', "api/user", "", token, callback);
};

API.prototype.getPublicKeyForUser = function (token, username, callback)
{
    this.sendAuthRequest('get', "api/user/" + username + "/public_key", "", token, callback);
};


API.prototype.sendMessage = function (token, recipient, content, callback)
{
    var data = {};
    data["recipient"] = recipient;
    data["content"] = content;
    this.sendAuthRequest('post', "api/message", data, token, callback);
};


API.prototype.getInbox = function (token, callback)
{
    this.sendAuthRequest('get', "api/inbox", "", token, callback);
};


API.prototype.deleteMessageWithId = function (token, m_id, callback)
{
    this.sendAuthRequest('delete', "api/message/" + m_id, "", token, callback);
};


API.prototype.getDashboard = function (token, callback)
{
    this.sendAuthRequest('get', "api/dashboard", "", token, callback);
};


API.prototype.sendRequest = function (rtype, rurl, rdata, callback)
{
    if(rtype == "get")
    {
        $.ajax({
             type: rtype,
             url: rurl,
             data: rdata,
             //contentType: "application/json; charset=utf-8",
             //crossDomain: true,
             dataType: "json",
             //traditional: true,
             success: function (data, status, jqXHR) {
                 callback(data);
             },

             error: function (jqXHR, status) {
                 // error handler
                 console.log(jqXHR);
                 alert('fail' + status.code);
             }
          });
    }
    else
    {
        $.ajax({
             type: rtype,
             url: rurl,
             data: JSON.stringify(rdata),
             contentType: "application/json; charset=utf-8",
             crossDomain: true,
             dataType: "json",
             success: function (data, status, jqXHR) {
                 callback(data);
             },

             error: function (jqXHR, status) {
                 // error handler
                 console.log(jqXHR);
                 alert('fail' + status.code);
             }
          });
    }

	
};


API.prototype.sendAuthRequest = function (rtype, rurl, rdata, token, callback)
{
    if(rtype == "get")
    {
        $.ajax({
             type: rtype,
             url: rurl,
             data: rdata,
             //contentType: "application/json; charset=utf-8",
             //crossDomain: true,
             dataType: "json",
             traditional: true,
             beforeSend : function(xhr) {
              // set header
              xhr.setRequestHeader("Authorization", "Token " + token);
            },
             success: function (data, status, jqXHR) {
                 callback(data);
             },

             error: function (jqXHR, status) {
                 // error handler
                 console.log(jqXHR);
                 alert('fail' + status.code);
             }
          });
    }
    else
    {
        $.ajax({
             type: rtype,
             url: rurl,
             data: JSON.stringify(rdata),
             contentType: "application/json; charset=utf-8",
             crossDomain: true,
             dataType: "json",
            beforeSend : function(xhr) {
              // set header
              xhr.setRequestHeader("Authorization", "Token " + token);
            },
             success: function (data, status, jqXHR) {
                 callback(data);
             },

             error: function (jqXHR, status) {
                 // error handler
                 console.log(jqXHR);
                 alert('fail' + status.code);
             }
          });
    }

    
};