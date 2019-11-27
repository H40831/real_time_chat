const chatLog = document.getElementById('chatLog');
const messageForm = document.getElementById('messageForm');
const messageFormData = ()=> new FormData(messageForm);
const messageArea = document.getElementById('messageArea');
const nameArea = document.getElementById('nameArea');
const sendMessageButton = document.getElementById('sendMessage');
const roomMenu = document.getElementById('roomMenu');
const roomMenuButton = document.getElementById('roomMenuButton');
const rooms = ()=> (Array.from( document.getElementsByClassName('rooms') ));

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

const addDBLog = (message)=> {//未作成
	const method = 'post';
	fetch('room_list.php',{
		method,
	})
	.then( response=> response.json() )
	.then( log=> { console.log(log) } )
	.catch( error=> { console.log(error) } );
};

const addWindowLog = (who,message)=>{//who:ユーザ(user) 他人(other) //message:メッセージ本文
	const isPositionBottom = chatLog.scrollTop + chatLog.clientHeight >= chatLog.scrollHeight - 30 ;//30は、最下行のテキストが読めるくらいの位置。
	console.log(isPositionBottom);
	const row = document.createElement('div');
	row.classList.add(who,'row');
	row.innerHTML = `
	<div class="whiteBox ${who} bubble">${message}</div>
	`;
	chatLog.appendChild(row);

	if(isPositionBottom){//もしスクロール位置が最下部だったら、
		chatLog.scrollTo(0,chatLog.scrollHeight);//新しい最下部までスクロールする。
	}
}

const getRoomList = ()=>{
	const method = 'post';
	return fetch('room_list.php',{
		method,
	})
	.then( response=> response.json() )
	.catch( error=> { console.log(error) } );
}

const setRoomList = ()=>{
	const roomList = document.getElementById('roomList');
	roomList.innerHTML = '';
	getRoomList()
	.then( responseData=> {
		responseData.forEach(
			i=> {
				const li = document.createElement('li');
				li.dataset.room = i.room_id;
				li.className = "rooms";
				li.innerHTML = i.room_name;
				roomList.appendChild(li);
			}
		)
	})
}
const switchRoomMenu = ()=>{
	if(roomMenu.classList.contains('hide')){
		setRoomList();
		roomMenu.className = "chatMenu shadow";
		roomMenuButton.className = "";
		window.setTimeout(
			()=>{roomMenuButton.className = "fas fa-chevron-up"},
			150
		)
	}else{
		roomMenu.className = "chatMenu shadow hide";
		roomMenuButton.className = "hide";
		window.setTimeout(
			()=>{roomMenuButton.className = "fas fa-chevron-down hide"},
			150
		)
	}
}

switchRoomMenu();
messageForm.onsubmit = ()=> {
	sendMessage();
	return false;
}

roomMenuButton.onclick = ()=> {
	switchRoomMenu();
}

rooms().forEach(
	room=>{
		room.onclick = ()=>{console.log(`${room.dataset.room}に移動します。`)};
	}
);//つぎやる: これを即時関数にする。