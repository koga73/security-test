<?php
	require_once "include/models/user.php";
	require_once "include/random_compat/random.php";
	
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

		public static function getUser(){
			if (!isset($_SESSION["user"])){
				throw new Exception("user is not logged in");
			}
			return $_SESSION["user"];
		}

		public static function generateNonce(){
			$token = bin2hex(random_bytes(64));
			$_SESSION["nonce"] = $token;
			return $token;
		}

		public static function verifyNonce($nonce){
			return $nonce === $_SESSION["nonce"];
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