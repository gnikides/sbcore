<?php namespace Story;

use Dir;
use Command;

class FileTool
{    
    /**
     * Remove annoying BOM from files
     *
     * @param   string  $str
     * @return  string
    */
    public static function removeBom($str)
    { 
        $str = trim($str);       
        if (substr($str, 0, 3) == pack("CCC",0xef,0xbb,0xbf)) {
            $str = substr($str, 9);
        }           
        return $str; 
    }
    
    /**
     * Write standard file
     * 
     * @param    string    $str                the file's content
     * @param     string    $file                the file's path
     * @return  boolean
    */
    public static function make($str, $file)
    {       
		clearstatcache();
		
		if ($tempFile = tempnam('/tmp','foo')) {
			
			if ($fp = fopen($tempFile, 'w')) {
				
				if (fwrite($fp, $str)) {
					
                    $dir = dirname($file);

					//  do some checking before renaming
					if (file_exists($file)) {
						self::delete($file);
					
                    } elseif (!file_exists($dir)) {
                        exec(escapeshellcmd('mkdir ' . $dir));
                        self::setPermissions('mkdir ' . $dir, 0777, true); 
					}

                    if (!file_exists($dir)) {
						if ($fp) {
							fclose($fp);
						}                           
						return false;
					}                     
					if (rename($tempFile, $file)) {
						self::setPermissions($file, null, false); 
						if ($fp) {
							fclose($fp);
						}
						//  no need to log on success - get too many file write statements
						return true;
					}                        
				}
			}
		}
        if (isset($fp)) {
            fclose($fp);
        }
        return false;
    }

    /**
     * Write standard file
     * 
     * @param    string    $str                the file's content
     * @param     string    $file                the file's path
     * @return  boolean
    */
    public static function create($path)
    {
        if ($fp = fopen($path, 'w')) {
        	self::setPermissions($path, null, false); 
			if ($fp) {
				fclose($fp);
			}
			return true;
        }
        if (isset($fp)) {
            fclose($fp);
        }
        return false;
    }
    
    /**
     * Append to file
     * 
     * @param    string    $str                the file's content
     * @param     string    $file                the file's path
     * @return  boolean
    */
    public static function appendTo($str, $file)
    {
        if (!empty($str)) {
            $str = $str;
            if ($fp = fopen($file, 'a+')) {                    
                if (fwrite($fp, $str)) {
                    fclose($fp);
                    //  no need to log on success - get too many file write statements
                    return true;
                }                        
            }
        }
        if (isset($fp)) {
            fclose($fp);
        }
        return false;
    }
    
    /**
     * Delete file
     * 
     * @param    string    $file
     * @return  boolean
    */
    public static function delete($file)
    {    
		exec(escapeshellcmd('rm -f ' . $file));
		return true;    
    } 
    
    /**
     * Set file/dir permissions
     *
     * @param   string     $path
     * @param   string     $permissions Optional
     * @param   boolean    $isDir
     * @return  void
    */    
    public static function setPermissions($path, $permissions='0755', $isDir=true) 
    {   
        if (empty($path)) {
            throw new Exception('Permission path is empty');
        }
        clearstatcache();
        if (file_exists($path)) {
            if (empty($permissions)) {
                $permissions = '0755';
            }   
            if (true == $isDir) {
                $r = '-R ';
            } else {
                $r = '';
            }
            $cmds[] = 'chmod ' . $r . $permissions . ' ' . $path;
            Command::run($cmds);
        }
        return true;
    }
    
    /**
     * Make symbolic link
     *
     * @param   string     $link
     * @param   string     $target
     * @param   string     $permissions Optional     
     * @return  boolean   true on success, false on failure
    */
    public static function makeSymLink($link, $target, $permissions=null) 
    { 
        if (file_exists($link)) {
            Command::run('rm ' . $link);
        }    
        Command::run('ln -sf ' . $target . ' ' . $link);
        self::setPermissions($link, $permissions, false);
        
        clearstatcache();
        if (file_exists($link)) {
            return true;
        }
        return false;
    } 
    
    /**
     * Copy files
     *
     * @param   array  $files
     * @return  mixed   true on success, false on failure
    */
    public static function batchCopy($files) 
    { 
        if (is_array($files)) {
        
            foreach ($files as $file) {

                clearstatcache();
                
                if (file_exists($file['source'])) {
                
                    if (file_exists($file['target'])) {                        
                        self::delete($file['target']);  
                    }    
                    if (!copy($file['source'], $file['target'])) {
                        throw new Exception('Could not copy ' . $file['source'] .  ' to ' . $file['target']);
                        return false;
                    }
                }    
            }            
        }
        return true;
    }  
    
    /**
     * Get array of info about a file
     *
     * @param   string  $file
     * @return  mixed   array on success, false on failure
    */    
    public static function getInfo($file)
    {
        clearstatcache();
        if (!file_exists($file)) {
            return false;
        }
        $data = array(
            'name' 			=> substr(strrchr($file, DIRECTORY_SEPARATOR), 1),
            'path' 			=> $file,
            'size' 			=> filesize($file),
            'date' 			=> filemtime($file),
            'readable' 		=> is_readable($file),
            'writable' 		=> is_writable($file),
            'executable' 	=> is_executable($file),
            'fileperms' 	=> fileperms($file)
        );    
        return $data;
    }

    /**
     * Get file extension
     *
     * ex html from /www/htdocs/index.html 
     *
     * @param   string  $file
     * @return  string
	*/ 
    public static function getExtension($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION);
    }

    /**
     * Get file basename
     *
     * ex  index.html from /www/htdocs/index.html 
     *
     * @param   string  $file
     * @return  string
	*/  
    public static function getBasename($file)
    {
        return pathinfo($file, PATHINFO_BASENAME);
    }

    /**
     * Get filename
     *
     * ex  index from /www/htdocs/index.html
     *
     * @param   string  $file
     * @return  string
	*/ 
    public static function getFilename($file)
    {
        return pathinfo($file, PATHINFO_FILENAME);
    }

    /**
     * Get dir from file
     *
     * ex /www/htdocs from /www/htdocs/index.html
     *
     * @param   string  $file
     * @return  string
	*/ 
    public static function getDirname($file)
    {
    	return pathinfo($file, PATHINFO_DIRNAME);
    }        
}