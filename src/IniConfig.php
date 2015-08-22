<?php
namespace HazeDevelopment;

class IniConfig
{
	public $config = array();
	public $path;

	public function __construct()
	{
		self::$path = config('iniconfig.file_path');
		self::$config = parse_ini_file(self::$path, true);
	}

	public function __get($name)
	{
		if($this->config[$name])
		{
			return $this->config[$name];	
		}
		else
		{
			return False;
		}
	}

	public function __set($name, $value)
	{
		self::$config[$name] = $value;
	}

	public static function all()
	{
		return self::$config;
	}

	private static function save()
	{
		$count = 0;
	    $res = array();
	    foreach(self::$config as $key => $val)
	    {
	        if(is_array($val))
	        {
	            $res[] = ($count != 0 ? "\r\n" : "").'['.$key.']';
	            foreach($val as $skey => $sval) $res[] = $skey." = '".$sval."'";
	        }
	        else $res[] = $key.' = '.$val;
	        $count ++;
	    } 

	    if (!$handle = fopen(self::$path, 'w')) { 
	        return false; 
	    }

	    $success = fwrite($handle, implode("\r\n", $res));
	    fclose($handle); 

	    return $success; 
	}
   
}