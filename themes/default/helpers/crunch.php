<?php
class Crunch
{
    var $files;

    public function __construct()
    {
        $this->files = [];
    }

    public function addFile($path)
    {
        if(!is_array($path))
        {
            $path = [$path];
        }

        foreach($path as $p)
        {
            $this->files[] = $p;
        }
        
    }
}

class CSSCrunch extends Crunch
{

    function crunch($dev=false)
    {
        $filename = md5(serialize($this->files)).'.css';

        if(!file_exists(theme_path().'assets/css/'.$filename.'.css'))
        {
            $buffer = "";

            foreach ($this->files as $cssFile) {
                $buffer .= file_get_contents(theme_path().'assets/css/'.$cssFile.'.css');

                if($dev)
                {
                    echo theme_css($cssFile.'.css', true);
                    continue;
                }
            }

            if($dev)
            {
                return;
            }

            // Remove comments
            $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
             
            // Remove space after colons
            $buffer = str_replace(': ', ':', $buffer);
             
            // Remove whitespace
            $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);

            file_put_contents(theme_path().'assets/css/'.$filename, $buffer);
        }
        
        
        echo '<link href="'.theme_css($filename).'" type="text/css" rel="stylesheet" />';

        $this->files = [];
    }

}

class JSCrunch extends Crunch
{

    function crunch($dev=false)
    {
        $filename = md5(serialize($this->files)).'.js';

        $buffer = "";

        if(!file_exists(theme_path().'assets/js/'.$filename))
        {
            foreach ($this->files as $jsFile) {
                $buffer .= file_get_contents(theme_path().'assets/js/'.$jsFile.'.js');

                if($dev)
                {
                    echo theme_js($jsFile.'.js', true);
                    continue;
                }
            }
            if($dev)
            {
                return;
            }

            file_put_contents(theme_path().'assets/js/'.$filename, $buffer);
        }

        echo '<script type="text/javascript" src="'.theme_js($filename).'"></script>';

        $this->files = [];
    }
}