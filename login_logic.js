const loginForm = document.getElementById('loginForm');
const loginFormData = ()=> new FormData(loginForm);

const loginButton = document.getElementById('loginButton');
const signupButton = document.getElementById('signupButton');
const pwForm = document.getElementById('pw');
const showPwButton = document.getElementById('showPwButton');

let showPw = false;
const ToggleShowPwForm = ()=>{
	showPw ? pwForm.type = 'text' : pwForm.type = 'password' ;
}
const ToggleShowPwIcon = ()=>{
	showPw ? showPwButton.className = "far fa-eye" : showPwButton.className = "far fa-eye-slash" ;
}
showPwButton.onclick = ()=>{
	showPw = !showPw;
	ToggleShowPwIcon();
	ToggleShowPwForm();
	return false;
}

const exitLoginWindow = ()=>{
	window.location.href = 'chat.php';
}

const loginFormSubmit = ( loginOrSignup )=> {//loginOrSignup: 関数呼出の際、文字列で'login'か'signup'かを明示する。
	const method = 'post';
	const body = loginFormData();
	body.append("loginOrSignup",loginOrSignup);//"loginOrSignup"はPHPとの共通文なので、名前を変えるとき注意。

	//console.log(...loginFormData().entries());//送信値チェック

	fetch('login_logic.php',{
		method,
		body
	})
	.then( response=> response.json() )
	.then( responseData=> responseData[0] )
	.then( loginResult=>{ 
		if( loginResult === ( 3 ) ){
			exitLoginWindow();
		}else{
			setLoginFormMessage(loginResult);
		}
	} )
	.catch( error=>{ setLoginFormMessage(error) } );
}


const setLoginFormMessage = (loginResult)=>{
	const message = document.getElementById('loginFormMessage');
	switch( loginResult ){
		case 0:
			message.innerText = '※ ログイン処理が実行されませんでした。';
			break;
		case 1:
			message.innerText = '※ 入力されていない欄があります。';
			break;
		case 2:
			message.innerText = '※ 一致するIDが存在しません。';
			break;
		case 3:
			message.innerText = '※ ログインしました。';　//正常であればログイン後すぐチャット画面へ移行するので、このメッセージは表示されない。
			break;
		case 4:
			message.innerText = '※ パスワードが一致しません。';
			break;
		case 5:
			message.innerText = '※ このIDは既に使用されています。';
			break;
		case 6:
			message.innerText = '※ 新規登録しました。'; //正常であれば新規登録後すぐログイン処理へ移行するので、このメッセージは表示されない。
			break;
		default:
			message.innerText = loginResult;
			break;

	}
}

loginButton.onclick = ()=> {
	loginFormSubmit( 'login' );
};
signupButton.onclick = ()=> {
	loginFormSubmit( 'signup' );
};
