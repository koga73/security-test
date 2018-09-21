<?php
	require_once "./models/user.php";

	class DB {
		private static $DB_ADDRESS = "127.0.0.1";
		private static $DB_USER = "root";
		private static $DB_PASS = "";
		private static $DB_NAME = "security_test";

		private static $REGEX_USERNAME = "/^\\w{6,16}$/";
		private static $REGEX_PASSWORD = "/^[\\w`~!@#$%^&*()-=+,<\.>\/?;:\[{\]}|\\\s]{7,32}$/";

		private function _connect(){
			//DB Connection
			$conn = new mysqli(DB::$DB_ADDRESS, DB::$DB_USER, DB::$DB_PASS, DB::$DB_NAME);
			$connError = mysqli_connect_error();
			if ($connError){
				throw new Exception("Database connection failed"); // . " " . $connError);
			}
			return $conn;
		}

		public function insertUser($username, $password){
			if (!preg_match(DB::$REGEX_USERNAME, $username)){
				throw new Exception("username invalid");
			}
			if (!preg_match(DB::$REGEX_PASSWORD, $password)){
				throw new Exception("password invalid");
			}
			$conn = $this->_connect();

			try {
				//Parameterized SQL to secure against SQLi
				$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
				if (!$stmt){
					throw new Exception("Statement invalid");
				}
				$stmt->bind_param("ss", $username, $password);

				//Insert user
				//Vulnerability: Password hashed using outdated SHA1 and not salted!
				//Fix: Use bcrypt hashing and salt with username + constant
				$password = sha1($password);
				$stmt->execute();
				$stmt->close();
			} catch (Exception $ex){
				throw $ex;
			} finally {
				$conn->close();
			}
			
		}

		public function login($username, $password){
			if (!preg_match(DB::$REGEX_USERNAME, $username)){
				throw new Exception("username invalid");
			}
			if (!preg_match(DB::$REGEX_PASSWORD, $password)){
				throw new Exception("password invalid");
			}
			$conn = $this->_connect();

			try {
				//Parameterized SQL to secure against SQLi
				$stmt = $conn->prepare("SELECT id, username FROM users WHERE username=? AND password=?");
				if (!$stmt){
					throw new Exception("Statement invalid");
				}
				$stmt->bind_param("ss", $username, $password);
	
				//Insert user
				//Vulnerability: Password hashed using outdated SHA1 and not salted!
				//Fix: Use bcrypt hashing and salt with username + constant
				$password = sha1($password);
				$stmt->execute();
				$stmt->store_result();
				$numRows = $stmt->num_rows;
				if ($numRows != 1){
					throw new Exception("Invalid login");
				}
				$stmt->bind_result($_id, $_username);
				$stmt->fetch();
				$stmt->close();

				return new User($_id, $_username);
			} catch (Exception $ex){
				throw $ex;
			} finally {
				$conn->close();
			}
		}
	}
?>