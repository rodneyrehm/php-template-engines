<?php
	class testFSWrapper
	{
		static private $files;

		private $file;
		private $read;

		static public function loadFilesystem($fs)
		{
			self::$files = array();
			$lines = file($fs);
			$currentFile = null;
			foreach($lines as $line)
			{
				if(strpos($line, '>>>>') === 0)
				{
					$currentFile = 'test://'.trim(substr($line, 4, strlen($line)));
					self::$files[$currentFile] = '';
					continue;
				}

				if(!is_null($currentFile))
				{
					self::$files[$currentFile] .= $line;
				}
			}
		} // end loadFilesystem();

		public function stream_open($path, $mode, $options, $opened_path)
		{
			$this->file = $path;
			if(!isset(self::$files[$path]))
			{
				return false;
			}
			return true;
		} // end stream_open();

		public function stream_close()
		{

		} // end stream_close();

		public function stream_eof()
		{
			return true;
			return ($this->read >= strlen(self::$files[$this->file]));
		} // end stream_eof();

		public function stream_read($count)
		{
			$return = substr(self::$files[$this->file], $this->read, $count);
			$this->read += $count;
			return $return;
		} // end stream_read();

		public function stream_write($data)
		{

		} // end stream_write();

		public function stream_tell()
		{
			return $this->read;
		} // end stream_tell();

		public function stream_seek($offset, $whence)
		{

		} // end stream_seek();

		public function stream_stat()
		{
			if(!isset(self::$files[$this->file]))
			{
				return array();
			}
			return array('size' => strlen($this->file));
		} // end stream_stat();

		public function url_stat($path, $flags)
		{
			if(!isset(self::$files[$path]))
			{
				return false;
			}
			return array('size' => strlen($path));
		} // end url_stat();
	} // end testFSWrapper;

	stream_register_wrapper('test', 'testFSWrapper');
