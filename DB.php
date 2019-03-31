<?php
class DB
{
	private $conn;
	

	function __construct()
	{
		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "tileartist";

		// Create connection
		$this->conn = new mysqli($servername, $username, $password, $dbname);
		if ($this->conn->connect_error) {
			die("Can't connect to databese.");
		}
	}
	
	///USERS
	///===================================================================================
	function addUser($username, $password) {
		$stmt = $this->conn->prepare("SELECT users.password FROM users WHERE users.username = ?");
		$stmt->bind_param('s', $username);
		$stmt->execute();
		//$result = $this->conn->query($sql);
		$result = $stmt->get_result();
		$userCount = 0;
		if($result === false){
			echo "Unknown error";
			return -1;
		}
		
		while($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$userCount++;
		}
		if($userCount != 0)
			return -1;
		
		//Create entry
		$stmt = $this->conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)" );
		$password_hash = password_hash($password, PASSWORD_BCRYPT);
		$stmt->bind_param('ss', $username, $password_hash);
		$stmt->execute();
		$res = $stmt->get_result();
		return 0;
	}
	function changePassword($userID, $newPassword) {
		//Create entry
		$stmt = $this->conn->prepare("UPDATE users SET users.password = ? WHERE users.ID = ?");
		$password_hash = password_hash($newPassword, PASSWORD_BCRYPT);
		$stmt->bind_param('si', $password_hash, $userID);
		$stmt->execute();
		$res = $stmt->get_result();
		
		if( $this->conn->errno !== 0 )
			return false;
		return true;
	}
	function verifyUser($username, $password) {
		$stmt = $this->conn->prepare("SELECT users.password FROM users WHERE users.username = ?");
		$stmt->bind_param('s', $username);
		$stmt->execute();
		
		$result = $stmt->get_result();
		
		if($result === false){
			echo "Unknown error";
			return false;
		}
		
		$isCorrect = false;
		while($row = $result->fetch_array(MYSQLI_ASSOC)) {
			//multi users with the same name protection
			if($isCorrect)
				return false;
			$isCorrect = password_verify($password, $row['password']);
		}
		return $isCorrect;
	}
	function usernExists($username) {
		$stmt = $this->conn->prepare("SELECT ID FROM users WHERE users.username = ?");
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$result = $stmt->get_result();
		
		return ($result->num_rows >= 1);
	}
	function userExistsID($ID) {
		$stmt = $this->conn->prepare("SELECT ID FROM users WHERE users.ID = ?");
		$stmt->bind_param('i', $ID);
		$stmt->execute();
		$result = $stmt->get_result();
		
		return ($result->num_rows >= 1);
	}
	function getUserID($username) {
		$stmt = $this->conn->prepare("SELECT ID FROM users WHERE users.username = ?");
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$result = $stmt->get_result();
		
		if($result === false)
			return false;
		
		$row = $result->fetch_array(MYSQLI_ASSOC);
		return $row['ID'];
	}
	function getUserName($userID) {
		$stmt = $this->conn->prepare("SELECT username FROM users WHERE users.ID = ?");
		$stmt->bind_param('i', $userID);
		$stmt->execute();
		$result = $stmt->get_result();
		
		if($result === false)
			return false;
		
		$row = $result->fetch_array(MYSQLI_ASSOC);
		return $row['username'];
	}
	function searchForUsers($username) {
		$ret = array();
		$usernameB = $username . '%';
		$usernameM = '%' . $username . '%';
		
		//the same
		$stmt = $this->conn->prepare("SELECT ID, username FROM users WHERE username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result !== false){
			while($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$ret[] = $row;
			}
		}
		//starts with
		$stmt = $this->conn->prepare("SELECT ID, username FROM users WHERE username LIKE ? AND username != ?");
		$stmt->bind_param("ss", $usernameB, $username);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result !== false){
			while($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$ret[] = $row;
			}
		}
		//in middle
		$stmt = $this->conn->prepare("SELECT ID, username FROM users WHERE username LIKE ? AND username NOT LIKE ? AND username != ?");
		$stmt->bind_param("sss", $usernameM, $usernameB, $username);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result !== false){
			while($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$ret[] = $row;
			}
		}
		
		return $ret;
	}
	function getPopularUsernames()
	{
		$ret = array();		
		
		$stmt = $this->conn->prepare("
			SELECT users.ID, users.username, COUNT(follows.followedID) AS 'fame'
			FROM users
			LEFT JOIN follows ON follows.followedID = users.ID
			GROUP BY follows.followedID
			ORDER BY fame DESC, ID
		");
		$stmt->execute();
		$result = $stmt->get_result();
		if($result !== false){
			while($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$ret[] = $row;
			}
		}
		
		return $ret;
	}
	function addFriend($userID, $followedID)
	{
		/*$stmt = $this->conn->prepare("
			INSERT INTO follows (userID, followedID)
			VALUES (?, ?)
		");*/
		$stmt = $this->conn->prepare("
			INSERT INTO follows (userID, followedID)
			SELECT ?, ?
			WHERE NOT EXISTS (
				SELECT * FROM follows WHERE follows.userID = ? AND follows.followedID = ?
			)
		");
		$stmt->bind_param('iiii', $userID, $followedID, $userID, $followedID);
		$stmt->execute();
		$res = $stmt->get_result();
		
		if( $this->conn->errno !== 0 )
			return false;
		return true;
	}
	function removeFriend($userID, $followedID)
	{
		$stmt = $this->conn->prepare("
			DELETE FROM follows WHERE follows.userID = ? AND follows.followedID = ?
		");
		$stmt->bind_param('ii', $userID, $followedID);
		$stmt->execute();
		$res = $stmt->get_result();
		
		if( $this->conn->errno !== 0 )
			return false;
		return true;
	}
	function isAFollowingB($a, $b)
	{
		$stmt = $this->conn->prepare("SELECT * FROM follows WHERE follows.userID = ? AND follows.followedID = ?");
		$stmt->bind_param('ss', $a, $b);
		$stmt->execute();
		$result = $stmt->get_result();
		
		return ($result->num_rows >= 1);
	}
	function getFollows($of_ID)
	{
		$stmt = $this->conn->prepare("
			SELECT follows.followedID, users.username
			FROM follows
			INNER JOIN users ON users.ID = follows.followedID
			WHERE follows.userID = ?
		");
		$stmt->bind_param('i', $of_ID);
		$stmt->execute();
		$result = $stmt->get_result();
		
		if($result === false)
			return false;
		
		$ret = array();
		while($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$ret[] = $row;
		}
		return $ret;
	}
	
	function addHytra($userID, $ip)
	{
		//Create entry
		$stmt = $this->conn->prepare("INSERT INTO hytra_tabela (userID, timestamp, addr) VALUES (?, CURRENT_TIMESTAMP(), ?)" );
		
		$ipAddr = 0;
		for($i = 0; $i < 4; $i++){
			$pos = strpos($ip, '.');
			if($pos === false) {
				$pos = strlen($ip);
			}
			$ipA = substr($ip, 0, $pos);
			$ip = substr($ip, $pos + 1);
			
			$ipAddr <<= 8;
			$ipAddr += intval($ipA);
		}		
		
		$stmt->bind_param('ii', $userID, $ipAddr);
		$stmt->execute();
		$res = $stmt->get_result();
		return 0;
	}
	///===================================================================================
	
	///IAMGES
	///===================================================================================
	function getAllCategories()	
	{
		$sql = "SELECT category FROM images GROUP BY category";
		$result = $this->conn->query($sql);
		
		if($result === false){
			echo "Unknown error";
			return false;
		}
		
		$categories = array();
		while($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$categories[] = $row['category'];
		}
		return $categories;
	}
	function getFirstFreeID()
	{
		/*$sql = "SELECT MAX(ID) FROM images";
		$result = $this->conn->query($sql);
		
		if($result === false){
			echo "Unknown error";
			return false;
		}
		
		$row = $result->fetch_array(MYSQLI_ASSOC);
		return $row['MAX(ID)'] + 1;*/
		$sql = "SHOW TABLE STATUS LIKE 'images'";
		$result = $this->conn->query($sql);
		
		if($result === false){
			echo "Unknown error";
			return false;
		}
		
		$row = $result->fetch_array(MYSQLI_ASSOC);
		return $row['Auto_increment'];
	}
	function addNewEntry($title, $category, $authorID)
	{
		$stmt = $this->conn->prepare("INSERT INTO images (title, category, authorID) VALUES (?, ?, ?)");
		$stmt->bind_param('ssi', $title, $category, $authorID);
		$stmt->execute();
		$res = $stmt->get_result();
		
		if( $this->conn->errno !== 0 ){
			echo $this->conn->error;
			return false;
		}
		return true;
	}
	function removeEntry($imageID, $authorID)
	{
		$stmt = $this->conn->prepare("DELETE FROM images WHERE images.ID = ? AND images.authorID = ?");
		$stmt->bind_param('ii', $imageID, $authorID);
		$stmt->execute();
		$res = $stmt->get_result();
		
		if( $this->conn->errno !== 0 ){
			echo $this->conn->error;
			return false;
		}
		return true;
	}
	function getMasterpiece($id)
	{
		$stmt = $this->conn->prepare("SELECT * FROM images WHERE id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
		
		if($result === false){
			echo "Unknown error";
			return false;
		}
		
		return $result->fetch_array(MYSQLI_ASSOC);
	}
	function getAuthorsMasterpieces($authorID)
	{
		$stmt = $this->conn->prepare("
			SELECT images.ID, images.title, images.category, users.username AS 'author'
			FROM images
			INNER JOIN users ON users.ID = images.authorID
			WHERE authorID = ?"
		);
		$stmt->bind_param('i', $authorID);
		$stmt->execute();
		$result = $stmt->get_result();
		
		if($result === false){
			echo "Unknown error";
			return false;
		}
		
		$ret = array();
		while($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$ret[] = $row;
		}
		return $ret;
	}
	
	
	function getAllIDs()
	{
		$sql = "SELECT ID FROM images";
		$result = $this->conn->query($sql);
		
		if($result === false){
			echo "Unknown error";
			return false;
		}
		
		$IDs = array();
		while($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$IDs[] = $row['ID'];
		}
		return $IDs;
	}
	function getAllFriendsIDs($loggedInID)
	{
		$stmt = $this->conn->prepare("
			SELECT images.ID, images.title, images.category, images.authorID
			FROM images
			INNER JOIN follows ON follows.followedID = images.authorID
			WHERE follows.userID = ?
		");
		$stmt->bind_param('i', $loggedInID);
		$stmt->execute();
		$result = $stmt->get_result();
		
		if($result === false){
			echo "Unknown error";
			return false;
		}
		
		$IDs = array();
		while($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$IDs[] = $row['ID'];
		}
		return $IDs;
	}
	///===================================================================================
	
	///MULTIPLAYER GAMES
	///===================================================================================
	function multiplayerGames_initNew($imageID, $player1_id, $player2_id)
	{
		$stmt = $this->conn->prepare("INSERT INTO multiplayer_games (imageID, player1_id, player2_id) VALUES (?, ?, ?)");
		$stmt->bind_param('iii', $imageID, $player1_id, $player2_id);
		$stmt->execute();
		$res = $stmt->get_result();
		
		if($res === false && $this->conn->errno !== 0)
			return false;
		return true;
	}
	function multiplayerGames_getImageID($gameID)
	{
		$stmt = $this->conn->prepare("SELECT imageID FROM multiplayer_games WHERE ID = ?");
		$stmt->bind_param('i', $gameID);
		$stmt->execute();
		$result = $stmt->get_result();
		
		if($result === false)
			return false;
		
		$row = $result->fetch_array(MYSQLI_ASSOC);
		return $row['imageID'];
	}
	function multiplayerGames_update($playerID, $gameID, $time)
	{
		$stmt1 = $this->conn->prepare("UPDATE multiplayer_games SET player2_time = ? WHERE ID = ? AND player2_id = ?;");
		$stmt1->bind_param('iii', $time, $gameID, $playerID);
		$stmt1->execute();
		$stmt2 = $this->conn->prepare("UPDATE multiplayer_games SET player1_time = ? WHERE ID = ? AND player1_id = ?;");
		$stmt2->bind_param('iii', $time, $gameID, $playerID);
		$stmt2->execute();
		
		$res1 = $stmt1->get_result();
		$res2 = $stmt2->get_result();
		
		if( $this->conn->errno !== 0 ){
			echo $this->conn->error;
			return false;
		}
		return true;
	}
	
	function multiplayerGames_check($nickname)
	{
		$stmt = $this->conn->prepare("
			SELECT multiplayer_games.ID, multiplayer_games.imageID, player1.username AS 'player1_name', player2.username AS 'player2_name', multiplayer_games.player1_time, multiplayer_games.player2_time
			FROM multiplayer_games 
			INNER JOIN users as player1 ON multiplayer_games.player1_id = player1.ID
			INNER JOIN users as player2 ON multiplayer_games.player2_id = player2.ID
			WHERE player1.username = ? OR player2.username = ?
		");
		$stmt->bind_param('ss', $nickname, $nickname);
		$stmt->execute();
		$res = $stmt->get_result();
		
		if($res === false)
			return false;
		
		$games = array();
		while($row = $res->fetch_array(MYSQLI_ASSOC)) {
			$games[] = array(
				'ID' => $row['ID'],
				'imageID' => $row['imageID'],
				'player1_name' => $row['player1_name'],
				'player2_name' => $row['player2_name'],
				'player1_time' => $row['player1_time'],
				'player2_time' => $row['player2_time'],
			);
		}
		
		return $games;
	}
	///===================================================================================
}
?>