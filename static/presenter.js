/* initialize application */
var Presenter = function ()
{

}

Presenter.prototype.initAppView = function ()
{
	$( "#register_page" ).hide(function() {

	});
	$( "#login_page" ).hide(function() {

	});
	$( "#successful_registered_page" ).hide(function() {

	});

	$( "#dashboard_view" ).hide(function() {

	});

	$( "#inbox_view" ).hide(function() {

	});

	$( "#register_page" ).hide(function() {

	});

	$( "#loggedin_view" ).hide(function() {

	});

	$( "#compose_view" ).hide(function() {

	});

	$( "#read_msg_view" ).hide(function() {

	});
}

Presenter.prototype.initLoggedInView = function ()
{
	//remove navbar entries
	$("#menu_entry_login").remove();
	$("#menu_entry_register").remove();
	$("#menu_entry_about").remove();

	//init loggedin view and view dashboard
	$("#main_content").html( $("#loggedin_view").html() );
	create_dashboard_view();
}


Presenter.prototype.initDashboardView = function ()
{
	$("#loggedin_main_content").html( $("#dashboard_view").html() );
	api.getDashboard(user_session.token, function(response){
		$("#dashboard_invite_code").html(response.invite_code);
		$("#dashboard_num_of_msg").html(response.msg_num);
	})
}


Presenter.prototype.initComposeMessageView = function ()
{
	$("#loggedin_main_content").html( $("#compose_view").html() );
	api.getUsernames(user_session.token, function(usernames){
		usernames.forEach(function(username){
			$("#compose_users").append('<tr><td>'+ username +'</td><td><button type="button" class="btn btn-primary" onclick="addToRecipients(' + "'" + username + "'" + ')">add</button></td></tr>');
		})
	});
}


Presenter.prototype.initInboxView = function ()
{
	$("#loggedin_main_content").html( $("#inbox_view").html() );
	api.getInbox(user_session.token, function(messages){
		user_messages = messages;

		messages.forEach(function(message){
			$("#inbox_messages").append('<tr><td> <a href="#" onclick="readMessage(' + "'" + message.m_id + "'" + ')">'+ message.sender +'</a></td><td>' + message.date + '</td><td><button type="button" class="btn btn-danger" onclick="deleteMessage(' + "'" + message.m_id + "'" + ')">delete</button></td></tr>');
		})
	});
}

Presenter.prototype.initReadMessageView = function (m_id)
{
	$("#loggedin_main_content").html( $("#read_msg_view").html() );
	user_messages.forEach(function(message){
		if(message.m_id == m_id)
		{
			var pgpMessage = message.content;
			pgpMessage = openpgp.message.readArmored(pgpMessage);
			user_session.private_key.decrypt(user_session.password);

			openpgp.decryptMessage(user_session.private_key, pgpMessage).then(function(plaintext) {
			    $("#read_message_content").html(escapeHtml(plaintext));
			    $("#read_message_sender").html(escapeHtml(message.sender));
			    $("#read_message_date").html(message.date);
			}).catch(function(error) {
			    // failure
			});
		}
	})
}