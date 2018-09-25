<?php
	require_once "include/models/user.php";

	class DB {
		private static $DB_ADDRESS = "127.0.0.1";
		private static $DB_USER = "root";
		private static $DB_PASS = "";
		private static $DB_NAME = "security_test";

		private static $REGEX_USERNAME = "/^\w{6,16}$/";
		private static $REGEX_PASSWORD = "/^[\w\s`~!@#$%^&*()-=+,<\.>\/?;:\[{\]}|\\'\"]{7,32}$/";
		private static $REGEX_MESSAGE = "/^[\w\s`~!@#$%^&*()-=+,<\.>\/?;:\[{\]}|\\'\"]{1,140}$/";

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
				//Parameterized SQL to protect against SQLi
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
				//Parameterized SQL to protect against SQLi
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

		public function insertMessage($user, $message){
			if (!preg_match(DB::$REGEX_MESSAGE, $message)){
				throw new Exception("message invalid");
			}
			$conn = $this->_connect();

			try {
				//Parameterized SQL to protect against SQLi
				$stmt = $conn->prepare("INSERT INTO messages (user_id, content) VALUES (?, ?)");
				if (!$stmt){
					throw new Exception("Statement invalid");
				}
				$stmt->bind_param("ss", $user->id, $message);

				//Vulnerability: XSS
				//Fix: TODO
				$stmt->execute();
				$stmt->close();
			} catch (Exception $ex){
				throw $ex;
			} finally {
				$conn->close();
			}
		}

		public function searchMessages($query = ""){
			$conn = $this->_connect();

			try {
				//Vulnerability: SQLi
				//Fix: Use parameterized SQL to protect against SQLi
				$sql = "SELECT users.username, messages.content, messages.created FROM messages INNER JOIN users ON users.id=messages.user_id WHERE messages.content LIKE '%" . $query . "%'";
				$result = $conn->query($sql);

				//Don't do this!
				$error = mysqli_error($conn);
				if ($error){
					throw new Exception($error);
				}

				$messages = [];
				if ($result && $result->num_rows){
					//Returning full data set, better to only return known fields!
					while ($row = $result->fetch_assoc()){
						array_push($messages, $row);
					}
				}
				return $messages;
			} catch (Exception $ex){
				throw $ex;
			} finally {
				$conn->close();
			}
		}
	}
?>