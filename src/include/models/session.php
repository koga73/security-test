<?php
	require_once "include/models/user.php";

	session_start();
	
	class Session {
		public static $TTL = 30 * 60; //Seconds

		public static function login($user){
			$_SESSION["user"] = $user;
		}

		public static function logout(){
			session_unset();
			session_destroy();
		}

		public static function isLoggedIn(){
			if (isset($_SESSION["user"])){
				return true;
			}
			return false;
		}
	}

	//Check expired
	if (Session::isLoggedIn()){
		if (isset($_SESSION["last"]) && (time() - $_SESSION["last"] > Session::$TTL)){
			Session::logout();
		} else {
			$_SESSION["last"] = time();
		}
	}
?>