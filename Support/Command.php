<?php namespace Story;

class Command
{    
    /**
     * Run more than one command-line program
     *
     * @param  array  $cmds
     * @param  string  $message
     * @param  boolean  $isOutput   default is that we don't print the command output
     * @return  mixed
    */
    public static function runCommand($cmd, $isOutput=false)
    {
        if (is_string($cmd)) {
            exec(escapeshellcmd($cmd), $output);          
        }
        if ($isOutput == true) {
            return $output;
        }    
        return true;        
    }
    
    /**
     * Run more than one command-line program
     *
     * @param  array  $cmds
     * @param  string  $message
     * @param  boolean  $isOutput   default is that we don't print the command output
     * @return  mixed
    */
    public static function run($cmds, $message='', $isOutput=false)
    {
        $outputs = array();
        
        if (is_array($cmds)) {
            foreach ($cmds as $cmd) {
                $outputs[] = self::runCommand($cmd, $isOutput);               
            }
        } elseif (is_string($cmds)) {
            $outputs[] = self::runCommand($cmds, $isOutput);           
        }
        if ($isOutput == true) {
            return $outputs;
        }    
        return true;
    }

    /**
     * Run more than one command-line program
     *
     * @param  array  $cmds
     * @param  string  $message
     * @param  boolean  $isOutput   default is that we don't print the command output
     * @return  mixed
    */
    public static function checkOutput($output, $errorString)
    {    
        if (stripos($output, $errorString) === true) {
            return false;
        }
        return true;
    } 
    
    /**
     * Run more than one command-line program
     *
     * @param  array  $cmds
     * @param  string  $message
     * @param  boolean  $isOutput   default is that we don't print the command output
     * @return  mixed
    */
    public static function checkOutputs($outputs, $errorString)
    {    
        if (is_array($outputs)) {
            foreach ($outputs as $v) {
                if (is_array($v)) {
                    if (!self::checkOutputs($v, $errorString)) {
                        return false;
                    }
                }
            }
        } elseif (is_string($outputs)) {
            if (!self::checkOutput($outputs, $errorString)) {
                return false;
            }
        }
        return true;
    }    
}