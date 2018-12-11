<?php namespace Story;

use Command;

class DirTool
{
    /**
    * @var array
    */
    public static $skipArray = array(
        '.',
        '..',
        '.svn',
        '.DS_Store',
        '.htaccess'    
    );

    /**
     *  Make directory
     * 
     * @param   string    $path
     * @return  mixed    string $path on success, false on failure
    */
    public static function make($path, $permissions=0777)
    {
        clearstatcache();
        if (file_exists($path)) {
            chmod($path, $permissions); 
            return $path;
        } else {
            umask(0000);
            $old = error_reporting();
            error_reporting(E_ERROR | E_PARSE);
            @mkdir($path, $permissions, true);
            error_reporting($old);
        }    
        return $path;
    } 

    /**
     * Check directory exists
     * 
     * @param   string   $dir
     * @param   string   $permissions
     * @return  boolean
    */  
    public static function checkExist($dir, $permissions='')
    {
        clearstatcache();
        
        if (empty($dir)) {
            throw new Exception('Target not set');
            return false;
        } elseif (!file_exists($dir)) {
            Command::run('mkdir ' . $dir);            
        }
                
        clearstatcache();
        if (!file_exists($dir)) {
            throw new Exception("Target $dir could not be found");        
            return false;
        }            
        File::setPermissions($dir, $permissions);
        return true;  
    }

    /**
     * Batch check directories exist
     * 
     * @param   array   $dirs
     * @param   string   $permissions
     * @return  boolean
    */  
    public static function batchCheckExist($dirs, $permissions='')
    {
        if (is_array($dirs)) {        
            foreach ($dirs as $dir) {
                if (!self::checkExist($dir, $permissions)) {
                    return false;
                }
            }
            return true;
        }    
        return false;  
    }
    
    /**
     * Get array of files in a directory
     *
     * @param    string       $dir
     * @param    bool         $skipArray    Optional 
     * @param    array        $skipArray    Optional     
     * @return    array
    */
    public static function makeArray($dir, $isFullPath=false, $skipArray=array())
    {   
        if (!$skipArray) {
            $skipArray = self::$skipArray;
        }              
        $array = array();
        
        if ($handle = dir($dir)) {                  
            while (false !== ($file = $handle->read())) {
                if (!in_array($file, $skipArray)) {
                    if ($isFullPath == true) {
                    	$array[] = $dir . DS . $file; 
                    } else {
                    	$array[] = $file;
                    }	
                }
            }
        }    
        return $array;
    }
    
    public static function scandirRecursive($dir, $isFullPath=false, $skipArray=array())
    {
        if (!$skipArray) {
            $skipArray = self::$skipArray;
        }              
        $array = array();
            
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $filename => $file) {			
			if (!in_array($file->getFileName(), $skipArray)) {				
				if ($isFullPath == true) {
					$array[] = $file->getPathName(); 
				} else {
					$array[] = $file->getFileName();
				}	
			}	
    	}
    	return $array;    
    }
    
    public static function isEmpty($dir)
    {
    	if (count(self::makeArray($dir)) > 0) {
    		return false;
    	}
    	return true;	
    }
    
    /**
     * Copy directory
     *
     * TRICKY- copy_dir is called recursively within method
     *
     * @param    string     $source
     * @param    string     $destination
     * @return    mixed    number of dir's copied, or false on failure
    */  
    public static function copyDir($source, $destination)
    {
        $dir = opendir($source); 
        
        if (file_exists($destination)) {
            return 0;
        } 
        
        mkdir($destination, fileperms($source));
        chmod($destination, 0777);
        $total = 0; 
        
        while ($file = readdir($dir))
        { 
            if (!in_array($file, self::$skipArray)){ 
    
                if (is_dir($source . '/' . $file)) { 
                    $total += self::copyDir($source . '/' . $file, $destination . '/' . $file); 
                } else { 
                    clearstatcache();
                    if (file_exists($source . '/' . $file)) {
                        copy($source . '/' . $file, $destination . '/' . $file);
                    }    
                    clearstatcache();
                    if (file_exists($destination . '/' . $file)) {
                        chmod($destination . '/' . $file, 0777);
                    }    
                    $total++; 
                } 
            } 
        }
        return $total;
    }
    
    /**
     * Delete a file, or a folder and its contents
     *
     * @author      Aidan Lister <aidan@php.net>
     * @version     1.0.3
     * @link        http://aidanlister.com/repos/v/function.rmdirr.php
     * @param       string   $dirname    Directory to delete
     * @param       boolean  $removeTop  delete directory itself?     
     * @return      bool     Returns TRUE on success, FALSE on failure
     */
    public static function delete($dirname, $removeTop=true)
    {
        // Sanity check
        if (!file_exists($dirname)) {
            return false;
        }
    
        // Simple delete for a file
        if (is_file($dirname) || is_link($dirname)) {
            @chmod($dirname, 0777);
            $old = error_reporting();
            error_reporting(E_ERROR | E_PARSE);
            return @unlink($dirname);
            error_reporting($old);
        }
    
        // Loop through the folder
        $dir = dir($dirname);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            // Recurse
            self::delete($dirname . DS . $entry);
        }
    
        // Clean up
        $dir->close();
        
        if (true == $removeTop) {
            if (file_exists($dirname)) {
            	$old = error_reporting();
            	error_reporting(E_ERROR | E_PARSE);
            	return @rmdir($dirname);
            	error_reporting($old);
            }	
        }
        return true;
    }
 
    public static function remove($dir)
    {
    	if (empty($dir)) {
    		throw new Exception('Directory empty');
    	}	
		exec(escapeshellcmd('rm -rf ' . $dir));
    }
    
       
    /**
     * Delete all files from directories (but not directory)
     *
     * @param  array   $dirs     
     * @param  boolean $removeTop  delete directory itself?
     * @return mixed   true on success, false on failure
    */
    public static function batchDelete($dirs, $removeTop=false) 
    { 
        if (is_array($dirs)) {
            foreach ($dirs as $dir) {
                clearstatcache();
                if (file_exists($dir)) {
                    self::delete($dir, $removeTop);
                }    
            }
        }
        return true;
    } 
    
    /**
     * Make staggered numbered directories so as not overrun 1000 subdirectory limit
    */
    public static function makeStaggered($id=null, $limit=500) 
    { 
    	if (null == $id) {
    		return false;
    	}
    	$i = (floor((int)$id / $limit) * $limit) + 1;
		return (string)$i . '-' . (string)($i + $limit - 1);	     
	} 

    /**
     * List files in a directory
    */  
	public static function rGlob($pattern, $flags = 0)
	{
    	$files = glob($pattern, $flags); 
    	foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        	$files = array_merge(
                $files,
                self::rGlob($dir.'/'.basename($pattern), $flags)
            );
    	}
    	return $files;
    } 	        
}