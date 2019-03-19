<?php

class LXTelnet
{
	const DEBUG = false;

	public $sock;
	public $connected;

	public $err;
	public $errmsg;

	public function init()
	{
		//$this->sock = fsockopen('phermsinMCU.noip.me', '248', $err, $errmsg);
		$this->sock = fsockopen('192.168.1.254', '248', $err, $errmsg);
		if($err)
		{
			$this->err = $err;
			$this->errmsg = $errmsg;
			return false;
		}

		// wait buffer
		sleep(1);

		// read, clear buffer
		$msg = fread($this->sock, 1000);
		if(self::DEBUG)
		{
			echo $msg;
			echo "\n";
		}

		return true;
	}

	public function login()
	{
		$res = $this->cmd('admin');
		$res = $this->cmd('control');

		$res = (strpos($res, 'successfully') !== false);
		$this->connected = $res;

		return $res;
	}

	public function cmd($val, $delay = 0)
	{
		fwrite($this->sock, "{$val}\r\n");
		sleep($delay);

		$msg = fread($this->sock, 200);

		// read, clear buffer
		if(self::DEBUG)
		{
			echo $msg;
			echo "\n";
		}

		return $msg;
	}

	public function control($val)
	{
		if(!$this->connected)
		{
			/*echo 'no connection';
			return;*/
			return false;
		}

		if($this->connected)
		{
			$data = $this->cmd($val);
			return $data;
		}

		return false;
	}

	public function status()
	{
		$data = $this->cmd('$00R0#65', 1);
		$data = substr($data, 5, 15);

		if(strlen($data) == 15)
			return str_split($data);

		return false;
	}

	public function close()
	{
		fclose($this->sock);
	}
}
/*
$lx = new LXTelnet();
if($lx->init() === false){ return; }
if($lx->login() === false){ return; }

print_r($lx->status());
*/