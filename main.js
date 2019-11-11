const loginForm = document.getElementById('loginForm');
const loginFormData = ()=> new FormData(loginForm);

const loginButton = document.getElementById('loginButton');
const signupButton = document.getElementById('signupButton');

const loginFormSubmit = ( loginOrSignup )=> {//loginOrSignup: 関数呼出の際、文字列で'login'か'signup'かを明示する。
	const method = 'post';
	const body = loginFormData();
	body.append("loginOrSignup",loginOrSignup);//"loginOrSignup"はPHPとの共通文なので、名前を変えるとき注意。

	//console.log(...loginFormData().entries());//値チェック

	fetch('login.php',{
		method,
		body
	})
	.then( response=> response.json() )
	.then( responseData=> { console.log(responseData) } )
	.catch( error=> { console.log(error) } );
}

loginButton.onclick = ()=> {
	loginFormSubmit( 'login' );
};
signupButton.onclick = ()=> {
	loginFormSubmit( 'signup' );
};

//次やる: ログインボタン押下したときにでる、SyntaxError: Unexpected token D in JSON at position 0　の対処