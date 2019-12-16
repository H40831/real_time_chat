const socket = io.connect('ec2-52-195-2-97.ap-northeast-1.compute.amazonaws.com:8080');
const body = document.getElementsByTagName('body')[0];
const roomMenu = document.getElementById('roomMenu');
const roomMenuButton = document.getElementById('roomMenuButton');
const rooms = ()=> (Array.from( document.getElementsByClassName('rooms') ));
const roomList = document.getElementById('roomList');
const roomName = document.getElementById('roomName');
const addRoomButton = document.getElementById('addRoom');
const addRoomForm = document.getElementById('addRoomForm');
const chatLog = document.getElementById('chatLog');
const messageForm = document.getElementById('messageForm');
const messageFormData = ()=> new FormData(messageForm);
const messageArea = document.getElementById('messageArea');
const nameArea = document.getElementById('nameArea');
const sendMessageButton = document.getElementById('sendMessage');

socket.on( 'notice' , notice=>{ console.log(notice) } );
socket.on( 'getCurrentRoom' , ()=>{
	if( typeof currentRoom === 'undefined' ){ return }
	moveRooms(currentRoom,currentRoomName)
});

const fitViewHeight = (isResize)=>{
	const style = isResize ?
		document.getElementsByTagName('style')[0]:
		document.createElement('style');
	style.innerText = `
		html,body{
			height: ${window.innerHeight}px;
		}
	`;
	if( !isResize ){
		body.insertBefore(style,headMenu);
	}
}
fitViewHeight(0);
window.addEventListener('resize', ()=>{fitViewHeight(1)});

const showRooms = ()=>socket.emit('rooms',function(i){console.log(i)});

const loadInitialInfo = ()=> {//最下行で実行。
	const method = 'post';
	return fetch('chat_onload.php',{
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
	.catch( error=> { throw error } );
};

const messageClear = ()=> {
	messageArea.value = '';
}

const sendMessage = ()=> {
	/*PHP(PDO)
	const method = 'post';
	const body = messageFormData();
	console.log(...messageFormData().entries());//送信値チェック
	messageClear();
	fetch('chat_logic.php',{
		method, 
		body
	})
	.catch( error=>{ throw error } );
	*/
	const data = {
		talkValue: messageArea.value.replace(/\r?\n/g, '\r\n'),
		talkTime: moment().format('YYYY-MM-DD HH:mm:ss'),
		userId: window.userId,
		userName: window.nameArea.value,
		roomId: window.currentRoom,
	}
	socket.json.emit('sendMessage', data );
}
messageForm.onsubmit = ()=> {
	if(currentRoom && nameArea.value && messageArea.value){ 
		sendMessage();
		messageArea.value = '';
		console.log('送信成功')
	}else{
		console.log('送信失敗')
	}
	return false;
}

const pushLog = (who,message)=> {//未作成
	const method = 'post';
	fetch('room_list.php',{
		method,
	})
	.then( response=> response.json() )
	.then( log=> { console.log(log) } )
	.catch( error=> { throw error } );
};

const addLog = (who,data)=>{//who:ユーザ(user) 他人(other) //message:メッセージ本文
    const formatedTalkTime = moment(data.talk_time).format("MM/DD HH:mm")
	const row = document.createElement('div');
	row.classList.add(who,'row');
	row.innerHTML = `
    <p class="talkInfo" id="log${data.talk_id}" data-name="${data.user_name}">
        <span class="talkTime">${formatedTalkTime}</span>
    </p>
	<div class="whiteBox ${who} bubble">
		${data.talk_value}
	</div>
	`;
	chatLog.appendChild(row);
}

const loadChatLogs = ( logs )=>{
	chatLog.innerHTML = "";
	console.log(logs);

	logs.forEach( log=>{
		log.user_id === userId ?
			addLog( 'user', log ):
			addLog( 'other', log );
	})
};

const logPositionIsBottom = ()=>( chatLog.scrollTop + chatLog.clientHeight >= chatLog.scrollHeight - 30 );//30は、最下行のテキストが読めるくらいの位置。
const logScrollHold = (currentScrollPosition)=>{
	chatLog.scrollTo(0,currentScrollPosition);
};
const logScrollBottom = ()=>{
	chatLog.scrollTo(0,chatLog.scrollHeight);
};

const reloadChatLogs = ()=>{
	const wasBottom = logPositionIsBottom();
	const scrollPosition = chatLog.scrollTop;
	const method = 'post';
	const body = new FormData();
	body.append('room_id',currentRoom);
	fetch('move_room.php',{
		method,
		body
	})
	.then( response=> response.json() )
	.then( logs=> { loadChatLogs( logs ); } )
	.then( ()=>{ wasBottom ? logScrollBottom() : logScrollHold(scrollPosition) } )
	.catch( error=> { throw error; } );
}
socket.on( 'reload' , ()=>{ reloadChatLogs() });

const moveRooms = ( roomId,roomName )=>{
	console.log(`Room ID ${roomId}: ${roomName}に移動します。`)
	window.currentRoom = roomId;
	socket.emit( 'moveRooms', currentRoom );
	window.roomName.innerText = roomName;

	const method = 'post';
	const body = new FormData();
	body.append('room_id',roomId);
	fetch('move_room.php',{
		method,
		body
	})
	.then( response=> response.json() )
	.then( logs=> { loadChatLogs( logs ); } )
	.then( ()=>{ logScrollBottom() } )
	.catch( error=> { throw error; } );
}

const loadRoomList = ()=>{
	const method = 'post';
	return fetch('room_list.php',{
		method,
	})
	.then( response=> response.json() )
	.catch( error=> { throw error } );
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
roomMenuButton.onclick = ()=> {
	switchRoomMenu();
}
loadInitialInfo()
.then( ()=>{ switchRoomMenu() } );

const toggleAddRoomForm = ()=>{
	addRoomForm.classList.toggle("hide");
};

window.addRoomCancelButton = document.createElement('button');
addRoomCancelButton.id = "addRoomCancel";
addRoomCancelButton.className = "fas fa-times floatButton shadow";
addRoomCancelButton.dataset.label = "Cancel";

const toggleAddRoomCancelButton = ()=>{
	const addRoomCancelButton = document.getElementById('addRoomCancel');
	if(addRoomCancelButton === null){
		roomMenu.insertBefore( window.addRoomCancelButton, addRoomForm );
		const addRoomCancelButton = document.getElementById('addRoomCancel');
		window.setTimeout(
			()=>{addRoomCancelButton.classList.add('active')},
			0
		)

	}else{
		addRoomCancelButton.classList.remove('active');

		window.setTimeout(
			()=>{addRoomCancelButton.parentNode.removeChild( addRoomCancelButton )},
			200
		)
		
	}
};
addRoomButton.onclick = ()=> {
	addRoom.classList.toggle('active');
	toggleAddRoomForm();
	toggleAddRoomCancelButton();
};
