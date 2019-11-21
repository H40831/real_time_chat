const messageForm = document.getElementById('messageForm');
const messageFormData = ()=> new FormData(messageForm);
const messageArea = document.getElementById('messageArea');
const nameArea = document.getElementById('nameArea');
const sendMessageButton = document.getElementById('sendMessage');

const setInitialInfo = (()=> {
	const method = 'post';
	fetch('chat_onload.php',{
		method,
	})
	.then( response=> response.json() )
	.then( userInfo=>{ 
		nameArea.value = userInfo.user_name;
	})
	.catch( error=>{ console.log(error) } );
})();

const messageClear = ()=> {
	messageArea.value = '';
}

messageForm.onsubmit = ()=> {
	sendMessage();
	return false;
}
const sendMessage = ()=> {
	const method = 'post';
	const body = messageFormData();
	console.log(...messageFormData().entries());//送信値チェック

	fetch('chat_logic.php',{
		method,
		body
	})
	//.then( response=> response.json() )
	//.then( responseData=> responseData[0] )
	.then( messageClear() )
	.catch( error=>{ console.log(error) } );
}