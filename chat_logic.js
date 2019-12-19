
const socket = io.connect('ec2-52-195-2-97.ap-northeast-1.compute.amazonaws.com:8080');
const body = document.getElementsByTagName('body')[0];
const roomMenu = document.getElementById('roomMenu');
const roomMenuButton = document.getElementById('roomMenuButton');
const room = ()=> (Array.from( document.getElementsByClassName('room') ));
const roomList = document.getElementById('roomList');
const roomName = document.getElementById('roomName');
const addMemberButton = document.getElementById('addMemberButton');
const addMemberForm = document.getElementById('addMemberForm');
const addMemberArea = document.getElementById('addMemberArea');
const addMemberSubmit = document.getElementById('addMemberSubmit');
const addRoomButton = document.getElementById('addRoom');
const addRoomForm = document.getElementById('addRoomForm');
const roomNameArea = document.getElementById('roomNameArea');
const roomMemberArea = document.getElementById('roomMemberArea');
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

const errorMessage = function(parentNode,message,positionTop,margin){
	if(document.getElementById('error')){ document.getElementById('error').remove() }
	if(!arguments){return}

	if(!errorMessage.arguments){return}
	const error = document.createElement('p');
	error.id="error";
	error.className="chatMenu";
	error.setAttribute('style',`top:${positionTop};margin:0 ${margin};`);
	error.innerText=message;
	parentNode.appendChild(error);
}

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
	const method = 'post';
	const body = new FormData();
	if( roomId ){
		console.log(`Room ID ${roomId}: ${roomName}に移動します。`)
		body.append('room_id', roomId );
		window.addMemberButton.classList.remove('hide');
		window.roomName.innerText = roomName;
		window.currentRoom = roomId;
		socket.emit( 'moveRooms', currentRoom );
	}else{
		window.creentRoom = undefined;
		window.roomName.innerText = "";
		socket.emit( 'moveRooms' );
		window.addMemberButton.classList.add('hide');
	}
	fetch('move_room.php',{
		method,
		body
	})
	.then( response=> response.json() )
	.then( logs=> {
		if(logs[0]==="error"){
			throw logs[1]
		}else{
			loadChatLogs( logs );
		}
	})
	.then( ()=>{ logScrollBottom() } )
	.catch( error=> { console.log(error) } )
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
				const room = document.createElement('span');
				room.dataset.room = i.room_id;
				room.className = "room"
				room.innerText = i.room_name;
				li.appendChild(room);
				const exitButton = document.createElement('button');
				exitButton.dataset.room = i.room_id;
				exitButton.className = "far fa-times-circle exit";
				li.appendChild(exitButton);
				roomList.appendChild(li);
			}
		)
	})
	.then( ()=>{ appendMoveRooms() } )
}
const appendMoveRooms = ()=>{
	room().forEach(
		room=>{
			const li = room.parentNode;
			room.onclick = ()=>{
				moveRooms( room.dataset.room, room.innerText );
				switchRoomMenu();
			};
			const exitButton = li.getElementsByClassName('exit')[0];
			exitButton.onclick = ()=>{
				exitRoom( room.dataset.room );
				li.classList.add('hide');
				window.setTimeout(
					()=>{ li.setAttribute('style','display:none;'); }
					,200
				)
			}
		}
	);
}

const exitRoom = ( roomId )=>{//次やる:exitRoom関数を作る
	const method = 'post';
	const body = new FormData();
	body.append('room_id',roomId);
	fetch('exit_room.php',{
		method, 
		body
	})
	.then( response=> response.json() )
	.then( ()=>{if(roomId===currentRoom){
		moveRooms()
	}})
	.catch( error=>{ console.log(error) } );
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
	errorMessage();
}
loadInitialInfo()
.then( ()=>{ switchRoomMenu() } );

const toggleAddRoomForm = ()=>{
	addRoomForm.classList.toggle("active");
};

window.addRoomCancelButtonElement = document.createElement('button');
addRoomCancelButtonElement.id = "addRoomCancel";
addRoomCancelButtonElement.className = "fas fa-ban floatButton shadow center";
addRoomCancelButtonElement.dataset.label = "Cancel";

const toggleAddRoomCancelButton = ()=>{
	const addRoomButtons = document.getElementById('addRoomButtons');
	const addRoomCancelButton = document.getElementById('addRoomCancel');
	if(addRoomCancelButton === null){
		addRoomButtons.appendChild( addRoomCancelButtonElement );
		window.addRoomCancelButton = document.getElementById('addRoomCancel');
		window.setTimeout(
			()=>{window.addRoomCancelButton.classList.add('active')},
			0
		)
		window.addRoomCancelButton = document.getElementById('addRoomCancel');

		addRoomButton.onclick = ()=> {
			addRoom();
		}
		window.addRoomCancelButton.onclick = ()=>{
			toggleAddRoomForm();
			toggleAddRoomCancelButton();
		}

	}else{
		addRoomCancelButton.classList.remove('active');
		window.setTimeout(
			()=>{addRoomCancelButton.parentNode.removeChild( addRoomCancelButton )},
			200
		)
		window.addRoomCancelButton = null;

		addRoomButton.onclick = ()=> {
			toggleAddRoomForm();
			toggleAddRoomCancelButton();
		};
	}

};
addRoomButton.onclick = ()=> {
		toggleAddRoomForm();
		toggleAddRoomCancelButton();
};

const addRoom = ()=>{
	console.log('ルームを作成します');
	const roomName = roomNameArea.value;
	if(!roomName){
		console.log('送信失敗');
		return false;
	}
	const addMembers = roomMemberArea.value.split(",").filter(n=>n);
	const method = 'post';
	const body = new FormData();
	body.append('room_name',roomName);
	body.append('add_members',JSON.stringify(addMembers));
	console.log(...body.entries());//送信値チェック
	roomNameArea.value = "";
	roomMemberArea.value = "";
	fetch('add_room.php',{
		method, 
		body
	})
	.then( response=> response.json() )
	.then( addedRoom=> { moveRooms( addedRoom, roomName ) } )
	.then( ()=>{
		switchRoomMenu();
		toggleAddRoomForm();
		toggleAddRoomCancelButton();
	} )
	.catch( error=>{ console.log(error) } );
}

const toggleAddMemberForm = ()=>{
	addMemberForm.classList.toggle('hide');
}
addMemberButton.onclick = ()=>{ 
	toggleAddMemberForm();
	errorMessage();
}
addMemberArea.onkeydown = e=> {
	if(e.keyCode===27){//esc
		toggleAddMemberForm();
	}
}

const addMember = ()=>{
	const addMembers = addMemberArea.value.split(",").filter(n=>n);
	if( !addMembers.toString() ){
		errorMessage(addMemberForm,'追加したいユーザーを指定してください。','7.25em','1em')
		return; 
	}

	const method = 'post';
	const body = new FormData();
	body.append('add_members',JSON.stringify(addMembers));
	console.log(...body.entries());//送信値チェック
	fetch('add_room.php',{
		method, 
		body
	})
	.then( response=> response.json() )
	.then( addedMember=> { 
		if(addedMember[0]==="error"){
			throw addedMember[1]
		}else{
			console.log(addedMember)
		}
	})
	.then( ()=>{ toggleAddMemberForm() } )
	.then( ()=>{ addMemberArea.value = ""; } )
	.catch( error=>{ errorMessage(addMemberForm,error,'7.25em','1em') } );
}

addMemberSubmit.onclick = ()=>{addMember()}
