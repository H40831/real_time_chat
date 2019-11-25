const chatLog = document.getElementById('chatLog');
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
	.then( userInfo=> { nameArea.value = userInfo.user_name; } )
	.catch( error=> { console.log(error) } );
})();

const messageClear = ()=> {
	messageArea.value = '';
}

const sendMessage = ()=> {
	const method = 'post';
	const body = messageFormData();
	console.log(...messageFormData().entries());//送信値チェック

	fetch('chat_logic.php',{
		method,
		body
	})
	.then( response=> response.json() )
	.then( responseData=> {console.log(responseData)} )
	.then( messageClear() )
	.catch( error=>{ console.log(error) } );
}

const addLog = (who,message)=>{//who:ユーザ(user) 他人(other) //message:メッセージ本文
	const isPositionBottom = chatLog.scrollTop===chatLog.scrollHeight ;//←次やる：これ未完成！その1
	console.log(isPositionBottom);
	const row = document.createElement('div');
	row.classList.add(who,'row');
	row.innerHTML = `
	<div class="whiteBox ${who} bubble">${message}</div>
	`;
	chatLog.appendChild(row);

	if(isPositionBottom){//もしスクロール位置が最下部だったら、
		chatLog.scrollTo(0,chatLog.scrollHeight);//新しい最下部までスクロールする。←次やる：これ未完成！その2
	}
}

messageForm.onsubmit = ()=> {
	sendMessage();
	return false;
}