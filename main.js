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

//let loginResult = 0;
const loginFormSubmit = ( loginOrSignup )=> {//loginOrSignup: 関数呼出の際、文字列で'login'か'signup'かを明示する。
	const method = 'post';
	const body = loginFormData();
	body.append("loginOrSignup",loginOrSignup);//"loginOrSignup"はPHPとの共通文なので、名前を変えるとき注意。

	//console.log(...loginFormData().entries());//送信値チェック

	fetch('login.php',{
		method,
		body
	})
	.then( response=> response.json() )
	.then( responseData=> responseData[0] )
	.then( loginResult=>{ setLoginFormMessage(loginResult); } )
	.catch( error=>{ setLoginFormMessage(error) } );
}


const setLoginFormMessage = (loginResult)=>{
	console.log(loginResult);
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
			message.innerText = '※ ログインしました。';
			break;
		case 4:
			message.innerText = '※ パスワードが一致しません。';
			break;
		case 5:
			message.innerText = '※ このIDは既に使用されています。';
			break;
		case 6:
			message.innerText = '※ 新規登録しました。';
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
