const messageForm = document.getElementById('messageForm');
const messageFormData = ()=> new FormData(messageForm);
const sendMessageButton = document.getElementById('sendMessage');

messageForm.onsubmit = ()=> {
	sendMessage();
	return false;
}

const sendMessage = ()=> {
	const method = 'post';
	const body = messageFormData();
	console.log(...messageFormData().entries());//送信値チェック
	debugger;

	fetch('chat_logic.php',{
		method,
		body
	})
	//.then( response=> response.json() )
	//.then( responseData=> responseData[0] )
	.catch( error=>{ setLoginFormMessage(error) } );
}