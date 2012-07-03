<?php 
class Config
{
    private $_ini;
    
    public function __construct(array $ini = array())
    {
        $this->_ini = $ini;
    }
    
    public function load($filename, $env = false) 
    {
        if (!file_exists($filename)) {
            throw new RuntimeException("File {$filename} doesn't exists.");
        }
        $this->_ini = $this->_parse(
            parse_ini_file($filename, true),
            $env
        );
    }
    
    private function _parse($ini, $env)
    {
        foreach ($ini as $key => $value) {
            if (strpos($key, ":") !== false) {
                if ($env !== false) {
                    $ext = trim(substr($key, 0, strpos($key, ":")));
                    $base = trim(substr($key, strpos($key, ":")+1));
                    
                    if (!array_key_exists($base, $ini)) {
                        $ini[$base] = array();
                    }
                    
                    $ini[$base] = array_merge($ini[$base], $value);
                }
                unset($ini[$key]);
            }
        }
        
        $end = array();
        foreach ($ini as $k => $v) {
            $end[$k] = array();
            foreach ($v as $key => $value) {
                $t = $this->_processKey(array(), $key, $value);
                $end[$k] = array_merge_recursive($end[$k], $t);
            }
        }
        
        return $end;
    }
    
    protected function _processKey($config, $key, $value)
    {
        if (strpos($key, ".") !== false) {
            $pieces = explode(".", $key, 2);
            if (strlen($pieces[0]) && strlen($pieces[1])) {
                if (!isset($config[$pieces[0]])) {
                    $config[$pieces[0]] = array();
                } elseif (!is_array($config[$pieces[0]])) {
                    throw new RuntimeException("Cannot create sub-key for '{$pieces[0]}' as key already exists");
                }
                $config[$pieces[0]] = $this->_processKey($config[$pieces[0]], $pieces[1], $value);
            } else {
                throw new RuntimeException("Invalid key '$key'");
            }
        } else {
            $config[$key] = $value;
        }
        return $config;
    }
    
    public function exists($name)
    {
        return array_key_exists($name, $this->_ini);
    }
    
    public function __get($name)
    {
        if ($this->exists($name)) {
            if (is_array($this->_ini[$name])) {
                return new $this($this->_ini[$name]);
            } else {
                return $this->_ini[$name];
            }
        } else {
            return false;
        }
    }
    
    public function __call($method, $args) 
    {
        return new Config($this->_ini[$method]);
    }
    
    public function toArray()
    {
        return $this->_ini;
    }
    
    public function __set($name, $value)
    {
        throw new RuntimeException("You can't add elements at runtime..."); 
    }
}