/* app.js */




/* menu click handlers */

$( "#menu_register" ).click(function() {
  $("#main_content").html( $("#register_page").html());
});

$( "#menu_login" ).click(function() {
  $("#main_content").html( $("#login_page").html() );
});

$( "#compose_message" ).click(function() {
  create_compose_view();
});


function Session(username, public_key, private_key, salt, password, token) {
  this.username = username;
  this.public_key = public_key;
  this.private_key = private_key;
  this.token = token;
  this.salt = salt;
  this.password = password; 
}

var presenter = new Presenter();
var view_controller = new ViewController();
var user_session = new Session();
var api = new API();
var user_messages = "";

/* registration_page click handlers */
var pbkdf2_iterations = 1000;



function escapeHtml(text) {
  return text
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
}

