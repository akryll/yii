<?php

class UserController extends CController
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xEBF4FB,
			),
		);
	}

	/**
	 * Displays a login form to login a user.
	 */
	public function actionLogin()
	{
		$user=new LoginForm;
		if(Yii::app()->request->isPostRequest)
		{
			// collects user input data
			if(isset($_POST['LoginForm']))
				$user->setAttributes($_POST['LoginForm']);
			// validates user input and redirect to homepage if validated
			if($user->validate())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// displays the login form
		$this->render('login',array('user'=>$user));
	}

	/**
	 * Logout the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'UserController'.
 */
class LoginForm extends CFormModel
{
	public $username;
	public $password;
	public $rememberMe;
	public $verifyCode;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			array('username, password', 'required'),
			array('verifyCode', 'captcha', 'allowEmpty'=>!extension_loaded('gd')),
			array('password', 'authenticate'),
		);
	}

	/**
	 * Declares attribute labels.
	 * If not declared here, an attribute would have a label
	 * the same as the attribute name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'verifyCode'=>'Verification Code',
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())  // we only want to authenticate when no input errors
		{
			$duration=$this->rememberMe ? 3600*24*30 : 0; // 30 days
			if(!Yii::app()->user->login($this->username,$this->password,$duration))
				$this->addError('password','Incorrect password.');
		}
	}
}
