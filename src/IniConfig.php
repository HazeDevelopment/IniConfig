<?php
namespace HazeDevelopment;

/**
 * Class WriteIniFile
 *
 */
class IniConfig
{
    /**
     * @var string $path_to_ini_file
     * @var array $data_ini_file
     */
    protected static $path_to_ini_file = false;
    protected static $data_ini_file = false;

    /**
     * Constructor.
     *
     * @param string $ini_file
     */
    public function __construct()
    {
        self::init();
    }

    public static function init()
    {
        self::$path_to_ini_file = base_path(config('iniconfig.file_path'));
        if(!self::$path_to_ini_file || !self::$data_ini_file)
        {
            if (file_exists(self::$path_to_ini_file) === true) {
                self::$data_ini_file = @parse_ini_file(self::$path_to_ini_file, true);
            } else {
                self::$data_ini_file = [];
            }
            if (false === self::$data_ini_file) {
                throw new \Exception(sprintf('Unable to parse file ini : %s', self::$path_to_ini_file));
            }
        }
    }

    /**
     * method to get value already loaded
     *
     * @param string $name
     */
    public static function get($name, $default = false)
    {
        if(self::_isStatic())
        {
            self::init();
        }

        if(strpos($name, '.') !== false)
        {
            $splitted = explode(".", $name);
            $top = $splitted[0];
            $sub = $splitted[1];

            if(!array_key_exists($top, self::$data_ini_file))
            {
                return $default;
            }

            if(!array_key_exists($sub, self::$data_ini_file[$top]))
            {
                return $default;
            }

            return self::$data_ini_file[$top][$sub];
        }

        if(!array_key_exists($name, self::$data_ini_file))
        {
            return $default;
        }

        return self::$data_ini_file[$name];
    }

    /**
     * method to set value in the ini file.
     *
     * @param string $name
     * @param string $value
     */
    public static function set($name, $value)
    {
        if(self::_isStatic())
        {
            self::init();
        }


        if(strpos($name, '.') !== false)
        {
            $splitted = explode(".", $name);
            $top = $splitted[0];
            $sub = $splitted[1];

            if(!array_key_exists($top, self::$data_ini_file))
            {
                self::$data_ini_file[$top] = [];
                self::$data_ini_file[$top][$sub] = $value;
            }
            else
            {
                self::$data_ini_file[$top][$sub] = $value;
            }
        }
        else
        {
            self::$data_ini_file[$name] = $value;
        }
        
        return true;   
    }

    /**
     * method to check if ini file contains value
     * @param string $name
     */
    public static function has($name)
    {
        if(self::_isStatic())
        {
            self::init();
        }

        if(strpos($name, '.') !== false)
        {
            $splitted = explode(".", $name);
            $top = $splitted[0];
            $sub = $splitted[1];

            if(!array_key_exists($top, self::$data_ini_file))
            {
                return false;
            }
            else
            {
                if(!array_key_exists($sub, self::$data_ini_file[$top]))
                {
                    return false;
                }
            }

            return true;
        }

        if(!array_key_exists($name, self::$data_ini_file))
        {
            return false;
        }

        return true;
    }

    /**
     * method to get all values
     * @param string $name
     */
    public static function all()
    {
        if(self::_isStatic())
        {
            self::init();
        }

        return self::$data_ini_file;
    }

    /**
     * method for create ini file.
     *
     * @param array $new_ini_file
     */
    public function create(array $new_ini_file)
    {
        if(self::_isStatic())
        {
            self::init();
        }

        self::$data_ini_file = $new_ini_file;
    }

    /**
     * method for erase ini file.
     *
     */
    public function erase()
    {
        if(self::_isStatic())
        {
            self::init();
        }

        self::$data_ini_file = [];
    }

     /**
     * method for remove some values in the ini file.
     *
     * @param string $name
     */
     public static function delete($name)
     {
        if(self::_isStatic())
        {
            self::init();
        }

        if(strpos($name, '.') !== false)
        {
            $splitted = explode(".", $name);
            $top = $splitted[0];
            $sub = $splitted[1];

            if(array_key_exists($top, self::$data_ini_file))
            {
                if(array_key_exists($sub, self::$data_ini_file[$top]))
                {
                    unset(self::$data_ini_file[$top][$sub]);
                    return true;
                }
            }
        }

        if(array_key_exists($name, self::$data_ini_file))
        {
            unset(self::$data_ini_file[$name]);
            return true;
        }

        return false;
     }

    /**
     * method for write data in the ini file.
     *
     * @return bool true for a succes
     */
    public static function save()
    {
        if(self::_isStatic())
        {
            self::init();
        }

        $data_array = self::$data_ini_file;
        $file_content = null;
        foreach ($data_array as $key_1 => $group) {
            $file_content .= "\n[" . $key_1 . "]\n";
            foreach ($group as $key_2 => $value_2) {
                if (is_array($value_2)) {
                    foreach ($value_2 as $key_3 => $value_3) {
                        $file_content .= $key_2 . '[' . $key_3 . '] = ' . self::encode($value_3) . "\n";
                    }
                } else {
                    $file_content .= $key_2 . ' = ' . self::encode($value_2) . "\n";
                }
            }
        }
        $file_content = preg_replace('#^\n#', '', $file_content);
        $result = @file_put_contents(self::$path_to_ini_file, $file_content);
        if (false === $result) {
            throw new \Exception(sprintf('Unable to write in the file ini : %s', self::$path_to_ini_file));
        }
        return ($result !== false) ? true : false;
    }

    /**
     * method for encode type for ini file.
     *
     * @param mixed $value
     * @return string
     */
    private static function encode($value)
    {
        if ($value == '1' || $value === true) {
            return 1;
        }
        if ($value == '' || $value == '0' || $value === false) {
            return 0;
        }
        if (is_numeric($value)) {
            $value = $value * 1;
            if (is_int($value)) {
                return (int) $value;
            }
            if (is_float($value)) {
                return (float) $value;
            }
        }
        return '"' . $value . '"';
    }

    /**
     * Computes the difference of 2 arrays recursively
     * source : http://php.net/manual/en/function.array-diff.php#91756
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    private static function arrayDiffRecursive(array $array1, array $array2)
    {
        $finalArray = [];
        foreach ($array1 as $mKey => $mValue) {
            if (array_key_exists($mKey, $array2)) {
                if (is_array($mValue)) {
                    $arrayDiffRecursive = self::arrayDiffRecursive($mValue, $array2[$mKey]);
                    if (count($arrayDiffRecursive)) {
                        $finalArray[$mKey] = $arrayDiffRecursive;
                    }
                } else {
                    if ($mValue != $array2[$mKey]) {
                        $finalArray[$mKey] = $mValue;
                    }
                }
            } else {
                $finalArray[$mKey] = $mValue;
            }
        }
        return $finalArray;
    }

    /**
     * function to check if iniconfig is called statically
     **/
    private static function _isStatic() {
        $backtrace = debug_backtrace();

        // The 0th call is to _isStatic(), so we need to check the next
        // call down the stack.
        return $backtrace[1]['type'] == '::';
    }
}