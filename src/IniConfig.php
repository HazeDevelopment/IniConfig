<?php
namespace HazeDevelopment;

use \WriteIniFile\WriteIniFile;

class IniConfig
{
	public $ini;
	public static $path;
	public static $config;

	public function __construct()
	{
		$this->ini = new WriteIniFile(base_path(config('iniconfig.file_path')));
		dd($this->ini);
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

	public static function get($name)
	{
		if(self::$config[$name])
		{
			return self::$config[$name];	
		}
		else
		{
			return False;
		}
	}

	public static function set($name, $value)
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