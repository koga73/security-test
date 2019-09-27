<?php
	require_once "include/models/user.php";
	require_once "include/random_compat/random.php";

/* @if !SECURE */
	/* @if !HIDDEN_COMMENTS */
		//Vulnerability: Session fixation - these parameters will cause PHP to append session id in URL instead of cookie
		//Fix: Don't use these parameters, use default which is cookies
	/* @endif */
	ini_set("session.use_cookies", false);
	ini_set("session.use_only_cookies", false);
	ini_set("session.use_trans_sid", true);
	ini_set("session.auto_start", true);
/* @endif */
	session_name("sid");
	session_start();

	class Session {
/* @if SECURE */
		public static $COOKIES = true;
/* @endif */
/* @if !SECURE */
		public static $COOKIES = false;
/* @endif */
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

		//Note this assumes $url doesn't have a query string already
		public static function appendToUrl($url){
			if (!Session::$COOKIES){
				return $url . "?" . session_name() . '=' . session_id();
			}
			return $url;
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