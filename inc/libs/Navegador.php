<?php 
class Navegador{

/**
* Clase que nos ayuda a determinar la versión y el navegador usado por
* el cliente
*
* Ejemplo de uso:
* $browser = new Browser ;
* echo "$Browser->Name $Browser->Version"; 
**/
	
	private $props = array("version" => "0.0.0",
                            "name" => "unknown",
                            "agent" => "unknown") ;
    var $Name;
    var $Version;

    public function __Construct()
    {
        $browsers = array("firefox", "msie", "opera", "chrome", "safari",
                            "mozilla", "seamonkey", "konqueror", "netscape",
                            "gecko", "navigator", "mosaic", "lynx", "amaya",
                            "omniweb", "avant", "camino", "flock", "aol");

        $this->Agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        foreach($browsers as $browser)
        {
            if (preg_match("#($browser)[/ ]?([0-9.]*)#", $this->Agent, $match))
            {
                $this->Name = $match[1] ;
                $this->Version = $match[2] ;
                break ;
            }
        }
    }

    public function get($name)
    {
        if (!array_key_exists($name, $this->props))
        {
            echo 'No existe la propiedad o método $name';
            exit;
        }
        return $this->props[$name] ;
    }

    public function set($name, $val)
    {
        if (!array_key_exists($name, $this->props))
        {
            echo ("No existe la propiedad o método. Error al iniciar $name ".$this->props);
            exit;
        }
        $this->props[$name] = $val ;
    }

}
