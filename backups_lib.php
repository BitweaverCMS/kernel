<?php

class BackupLib extends BitBase {
	function BackupLib() {
		BitBase::BitBase();
	}

	function restore_database($filename) {
		global $gBitDbType;
		// Get the password before it's too late
		$query = "select `hash` from `".BIT_DB_PREFIX."users_users` where `user_id`=?";
		$pwd = $this->getOne($query,array(ROOT_USER_ID));

		switch ($gBitDbType) {
			case "postgres":
				$query = "select `tablename` from `pg_tables` where `tablename` not like 'pg_%' and `tablename` not like 'sql_%'";
				break;
			case "mysql":
				$query = "show tables";
		}

		$result = $this->query($query);
		$sql = '';
		$part = '';

		while ($res = $result->fetchRow()) {
			list($key, $val) = each($res);

			if (!strstr($val, 'babl')) {
				// Now delete the table contents
				$query2 = "delete from `$val`";
				$result2 = $this->query($query2);
			}
		}

//		$query = "update `".BIT_DB_PREFIX."users_users` set `hash`=? where `login`=?";
//		$result = $this->query($query,array($pwd,'admin'));
		@$fp = fopen($filename, "rb");

		if (!$fp) return false;

		while (!feof($fp)) {
			$rlen = fread($fp, 4);
			if (feof($fp)) break;

			$len = unpack("L", $rlen);
			$len = array_pop($len);
			$line = fread($fp, $len);

			//removing the de/cyphering stuff for now as it doesn't work on database restore
//			$line = $this->RC4($pwd, $line);

			// EXECUTE SQL SENTENCE HERE
			$result = $this->query($line,array());
		}

		fclose ($fp);
	}

	function RC4($pwd, $data) {
		$key[] = "";

		$box[] = "";
		$temp_swap = "";
		$pwd_length = 0;
		$pwd_length = strlen($pwd);

		for ($i = 0; $i <= 255; $i++) {
			$key[$i] = ord(substr($pwd, ($i % $pwd_length) + 1, 1));

			$box[$i] = $i;
		}

		$x = 0;

		for ($i = 0; $i < 255; $i++) {
			$x = ($x + $box[$i] + $key[$i]) % 256;

			$temp_swap = $box[$i];
			$box[$i] = $box[$x];
			$box[$x] = $temp_swap;
		}

		$temp = "";
		$k = "";
		$cipherby = "";
		$cipher = "";
		$a = 0;
		$j = 0;

		for ($i = 0; $i < strlen($data); $i++) {
			$a = ($a + 1) % 256;

			$j = ($j + $box[$a]) % 256;
			$temp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $temp;
			$k = $box[(($box[$a] + $box[$j]) % 256)];
			$cipherby = ord(substr($data, $i, 1)) ^ $k;
			$cipher .= chr($cipherby);
		}

		return $cipher;
	}

	// Functions to backup the database (mysql?)
	function backup_database($filename) {
		global $gBitDbType;
		ini_set("max_execution_time", "3000");

		$query = "select `hash` from `".BIT_DB_PREFIX."users_users` where `user_id`=?";
		$pwd = $this->getOne($query,array(ROOT_USER_ID));
		@$fp = fopen($filename, "w");

		if (!$fp)
			return false;

		switch ($gBitDbType) {
			case "postgres":
				$query = "select `tablename` from `pg_tables` where `tablename` not like 'pg_%' and `tablename` not like 'sql_%'";
				break;
			case "mysql":
				$query = "show tables";
		}

		$result = $this->query($query);
		$sql = '';
		$part = '';

		while ($res = $result->fetchRow()) {
			list($key, $val) = each($res);

			if (!strstr($val, 'babl')) {
				// Now dump the table
				$query2 = "select * from `$val`";

				$result2 = $this->query($query2);

				while ($res2 = $result2->fetchRow()) {
					$sentence = "values(";

					$first = 1;

					foreach ($res2 as $field => $value) {
						if (is_numeric($field) || empty($value))
							continue;
						if ($first) {
							$sentence .= "'" . addslashes($value). "'";
							$first = 0;
							$fields = '(`' . $field . '`';
						} else {
							$sentence .= ",'" . addslashes($value). "'";
							$fields .= ",`$field`";
						}
					}

					$fields .= ')';
					$sentence .= ")";
					$part = "insert into `$val` $fields $sentence;";
					$len = pack("L", strlen($part));
					fwrite($fp, $len);
					//removing the de/cyphering stuff for now as it doesn't work on database restore
//					$part = $this->RC4($pwd, $part);
					fwrite($fp, $part);
				}
			}
		}
		// And now print!
		fclose ($fp);
		return true;
	}
}

$backuplib = new BackupLib();

?>
