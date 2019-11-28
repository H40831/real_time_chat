const roomMenu = document.getElementById('roomMenu');
const roomMenuButton = document.getElementById('roomMenuButton');
const rooms = ()=> (Array.from( document.getElementsByClassName('rooms') ));
const roomList = document.getElementById('roomList');
const roomName = document.getElementById('roomName');
const chatLog = document.getElementById('chatLog');
const messageForm = document.getElementById('messageForm');
const messageFormData = ()=> new FormData(messageForm);
const messageArea = document.getElementById('messageArea');
const nameArea = document.getElementById('nameArea');
const sendMessageButton = document.getElementById('sendMessage');

const loadInitialInfo = (()=> {
	const method = 'post';
	fetch('chat_onload.php',{
		method,
	})
	.then( response=> response.json() )
	.then( userInfo=> {
		window.userId = userInfo.user_id;
		window.currentRoom = userInfo.current_room;
		window.currentRoomName = userInfo.current_room_name;
		nameArea.value = userInfo.user_name;
	})
	.then( ()=>{
		if(currentRoom){ 
			moveRooms( currentRoom, currentRoomName ) 
		}
	})
	.catch( error=> { console.log(error) } );
})();

const messageClear = ()=> {
	messageArea.value = '';
}

const sendMessage = ()=> {
	const method = 'post';
	const body = messageFormData();
	//console.log(...messageFormData().entries());//送信値チェック
	messageClear();
	fetch('chat_logic.php',{
		method,
		body
	})
	.catch( error=>{ console.log(error) } );
}

const pushLog = (who,message)=> {//未作成
	const method = 'post';
	fetch('room_list.php',{
		method,
	})
	.then( response=> response.json() )
	.then( log=> { console.log(log) } )
	.catch( error=> { console.log(error) } );
};

const addLog = (who,message)=>{//who:ユーザ(user) 他人(other) //message:メッセージ本文
	const isPositionBottom = chatLog.scrollTop + chatLog.clientHeight >= chatLog.scrollHeight - 30 ;//30は、最下行のテキストが読めるくらいの位置。
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

const loadChatLogs = ( logs )=>{
	chatLog.innerHTML = "";
	console.log(logs);

	logs.forEach( log=>{
		log.user_id === userId ?
			addLog( 'user', log.talk_value ):
			addLog( 'other', log.talk_value );
	})
};
const moveRooms = ( roomId,roomName )=>{
	console.log(`Room ID ${roomId}: ${roomName}に移動します。`)


	const method = 'post';
	const body = new FormData();
	body.append('room_id',roomId);
	fetch('move_room.php',{
		method,
		body
	})
	.then( response=> response.json() )
	.then( logs=> { loadChatLogs( logs ); } )
	.catch( error=> { console.log(error) } );

	roomName.innerText = roomName;
}
const appendMoveRooms = ()=>{
	rooms().forEach(
		room=>{
			room.onclick = ()=>{
				moveRooms( room.dataset.room, room.innerText );
				switchRoomMenu();
			};
		}
	);
}
const loadRoomList = ()=>{
	const method = 'post';
	return fetch('room_list.php',{
		method,
	})
	.then( response=> response.json() )
	.catch( error=> { console.log(error) } );
}
const addRoomList = ()=>{
	roomList.innerHTML = '';
	loadRoomList()
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
	.then( ()=>{ appendMoveRooms() } )
}
const switchRoomMenu = ()=>{
	if(roomMenu.classList.contains('hide')){
		addRoomList();
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
	if(messageArea.value){
		sendMessage();
	}
	return false;
}

roomMenuButton.onclick = ()=> {
	switchRoomMenu();
}

