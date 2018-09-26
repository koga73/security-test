<?php
	class Message {
		public $username;
		public $content;
		public $created;

		public function __construct($username, $content, $created){
			$this->username = $username;
			$this->content = $content;
			$this->created = $created;
		}

		public function __toString(){
			return $this->username . " " . $this->content . " " . $this.created;
		}
	}
?>