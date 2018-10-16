<?php
	require_once "include/models/user.php";
	require_once "include/models/message.php";

	class DB {
		private static $DB_ADDRESS = "127.0.0.1";
		private static $DB_USER = "root";
		private static $DB_PASS = "";
		private static $DB_NAME = "security_test";

		private static $REGEX_USERNAME = "/^\w{6,16}$/";
		private static $REGEX_PASSWORD = "/^[\w\s`~!@#$%^&*()-=+,<\.>\/?;:\[{\]}|\\'\"]{7,32}$/";
		private static $REGEX_MESSAGE = "/^[\w\s`~!@#$%^&*()-=+,<\.>\/?;:\[{\]}|\\'\"]{1,140}$/";

		private static function _connect(){
			//DB Connection
			$conn = new mysqli(DB::$DB_ADDRESS, DB::$DB_USER, DB::$DB_PASS, DB::$DB_NAME);
			$connError = mysqli_connect_error();
			if ($connError){
				throw new Exception("Database connection failed"); // . " " . $connError);
			}
			return $conn;
		}

		public static function insertUser($username, $password){
			if (!preg_match(DB::$REGEX_USERNAME, $username)){
				throw new Exception("username invalid");
			}
			if (!preg_match(DB::$REGEX_PASSWORD, $password)){
				throw new Exception("password invalid");
			}
			$conn = DB::_connect();

			try {
				//Parameterized SQL to protect against SQLi
				$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
				if (!$stmt){
					throw new Exception("Statement invalid");
				}
				$stmt->bind_param("ss", $username, $password);
/* @if SECURE */
				$password = password_hash($username . '-' . $password, PASSWORD_BCRYPT);
/* @endif */
/* @if !SECURE */
	/* @if !HIDDEN_COMMENTS */
				//Vulnerability: Password hashed using outdated SHA1 and not salted
				//Fix: Use bcrypt hashing and salt with username + constant
	/* @endif */
				$password = sha1($password);
/* @endif */
				$stmt->execute();
				$stmt->close();
			} catch (Exception $ex){
				throw $ex;
			} finally {
				$conn->close();
			}
		}

		public static function login($username, $password){
			if (!preg_match(DB::$REGEX_USERNAME, $username)){
				throw new Exception("username invalid");
			}
			if (!preg_match(DB::$REGEX_PASSWORD, $password)){
				throw new Exception("password invalid");
			}
			$conn = DB::_connect();

			try {
				//Parameterized SQL to protect against SQLi
				$stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username=?");
				if (!$stmt){
					throw new Exception("Statement invalid");
				}
				$stmt->bind_param("s", $username);
				$stmt->execute();
				$stmt->store_result();
				$numRows = $stmt->num_rows;
				if ($numRows != 1){
					throw new Exception("Invalid login");
				}
				$stmt->bind_result($_id, $_username, $_password);
				$stmt->fetch();
				$stmt->close();

/* @if SECURE */
				if (!password_verify($username . '-' . $password, $_password)){
					throw new Exception("Invalid login");
				}
/* @endif */
/* @if !SECURE */
				if ($_password !== sha1($password)){
					throw new Exception("Invalid login");
				}
/* @endif */
				return new User($_id, $_username);
			} catch (Exception $ex){
				throw $ex;
			} finally {
				$conn->close();
			}
		}

		public static function insertMessage($user, $message){
			if (!preg_match(DB::$REGEX_MESSAGE, $message)){
				throw new Exception("message invalid");
			}
			$conn = DB::_connect();

			try {
				//Parameterized SQL to protect against SQLi
				$stmt = $conn->prepare("INSERT INTO messages (user_id, content) VALUES (?, ?)");
				if (!$stmt){
					throw new Exception("Statement invalid");
				}
				$stmt->bind_param("ss", $user->id, $message);
				$stmt->execute();
				$stmt->close();
			} catch (Exception $ex){
				throw $ex;
			} finally {
				$conn->close();
			}
		}

		public static function searchMessages($query = ""){
			$conn = DB::_connect();

			try {
				//Vulnerability: SQLi
				//Fix: Use parameterized SQL to protect against SQLi
				$sql = "SELECT users.username, messages.content, messages.created FROM messages INNER JOIN users ON users.id=messages.user_id WHERE messages.content LIKE '%" . $query . "%' OR users.username='" . $query . "' ORDER BY messages.created DESC";
				$result = $conn->query($sql);

				//Don't do this!
				$error = mysqli_error($conn);
				if ($error){
					throw new Exception($error);
				}

				$messages = [];
				if ($result && $result->num_rows){
					while ($row = $result->fetch_assoc()){
						array_push($messages, new Message($row["username"], $row["content"], $row["created"]));
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