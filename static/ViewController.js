var ViewController = function ()
{

}

ViewController.prototype.btnRegisterClicked()
{
	alert("Your Browser might freeze for a moment while your cryptographic keys are created. Just wait for it to finish!");
	var saltObj = api.getNewSalt(function(data){
		var salt = data.salt;
		var options = {
		    numBits: 2048,
		    userId: $("#inputUsername").val(),
		    passphrase: $("#inputPassword").val()
		};

		openpgp.generateKeyPair(options).then(function(keypair) {
		    var privkey = keypair.privateKeyArmored;
		    var pubkey = keypair.publicKeyArmored;
		    var derivedKey = CryptoJS.PBKDF2($("#inputPassword").val(), salt, { keySize: 256/32, iterations: pbkdf2_iterations }).toString();
		    api.registerUser($("#inputUsername").val(), derivedKey, $("#inputInviteCode").val(), pubkey, privkey, salt, function (response){
		    	if(response.hasOwnProperty('error_message'))
		    	{
		    		toastr["error"](response.error_message, "Error");
		    	}
		    	$("#main_content").html( $("#successful_registered_page").html() );
		    });
		}).catch(function(error) {
		    toastr["error"](error.error_message, "Error");
		});
	});
}


ViewController.prototype.btnLoginClicked()
{
	var salt = "";
	var username = $("#inputUsernameLogin").val();
	var password = $("#inputPasswordLogin").val();
	api.getSaltForUser(username, function(response){
		salt = response.salt;
		 var derivedKey = CryptoJS.PBKDF2(password, salt, { keySize: 256/32, iterations: pbkdf2_iterations }).toString();
		 api.loginUser(username, derivedKey, function(response){
		 	if(response.hasOwnProperty('error_message'))
	    	{
	    		toastr["error"](response.error_message, "Error");
	    		return;
	    	}
		 	var unarmored_private_key = openpgp.key.readArmored(response.private_key).keys[0];
		 	user_session.username = username;
		 	user_session.salt = salt;
		 	user_session.token = response.token;
		 	user_session.private_key = unarmored_private_key;
		 	user_session.public_key = response.public_key;
		 	user_session.password = password;

		 	init_loggedin_view();
		 })
	})
}


ViewController.prototype.btnSendMessageClicked = function ()
{
	var recipientsList = $("#inputRecipientsCompose").val();
	var recipients = recipientsList.split(";");
	var content = $("#inputContentCompose").val();
	
	recipients.forEach(function(recipient){
		if(recipient != "")
		{
			getPublicKeyForUser(user_session.token, recipient, function(response){
				var publicKey = openpgp.key.readArmored(response.public_key);
				openpgp.encryptMessage(publicKey.keys, content).then(function(pgpMessage) {
				    // success
				    api.sendMessage(user_session.token, recipient, pgpMessage, function(response){
				    	if(response.hasOwnProperty('error_message'))
				    	{
				    		toastr["error"](response.error_message, "Error");
				    		return;
				    	}
				    	toastr["success"]("Message has been sent successfully!", "Message sent!");
				    	$("#inputRecipientsCompose").val("");
				    	$("#inputContentCompose").val("");
				    })
				}).catch(function(error) {
				    toastr["error"](error, "Error");
				});
			})
		}
	})
}


ViewController.prototype.addToRecipients = function (username)
{
	$("#inputRecipientsCompose").val( $("#inputRecipientsCompose").val() + username + ";" );
}

ViewController.prototype.deleteMessage(m_id) = function (m_id)
{
	api.deleteMessageWithId(user_session.token, m_id, function(response) {
		create_inbox_view();
	})
}