<?php

require_once plateforme_root.'utils/warningHandler.php';
require_once plateforme_root.'database/database.php';

class Cache {
    /**
     * Database object
     * @var Database Database object  
     */
    var $DB;
    
    /**
     * Cache table name
     * @var string cache table name ("cache" by default)
     */
    var $CACHE_TABLE = "cache";
    
    /**
     * Cache time in seconds
     * @var int Cache time in seconds (3600 by default, 1 hour) 
     */
    var $CACHE_TIME = 3600;
    
    /**
     * Constructeur
     * @param DB $db Database object
     */
    function __construct($db = null) {
        $this->DB = $db;
    }
    
    /**
     * Set a different Database
     * @param DB $db Database object
     */
    function set_db($db){
        $this->DB = $db;
    }
    
    /**
     * Set the cache time in seconds
     * @param int $cacheTime cache duration in seconds
     */
    function set_cache_time($cacheTime){
        $this->CACHE_TIME = $cacheTime;
    }
    
    /**
     * Retrieve the content of an url, from cache if it exists else it is stored in cache
     * @param string $url URL of the page to retrieve
     * @return string Content of the page
     */
    function get($url){
        $content = "";
        if ($this->DB != null){
            if (!$this->DB->is_connected()){
                $this->DB->connect();
            }
            $result = $this->DB->query('SELECT content, date FROM '.$this->CACHE_TABLE.' WHERE url = "'.addslashes($url).'" AND date > '.(time()-$this->CACHE_TIME).' LIMIT 0,1', false, true);
            if (count($result) > 0){
                $content = $result[0]['content'];
            }else{
                set_error_handler('WarningHandler::handleError');
                try{
                    $content = file_get_contents($url);
		    $headers = get_headers($url, 1);
		    $mimetype = "";
		    if (array_key_exists('Content-Type', $headers)){
			$mimetype = $headers['Content-Type'];
		    }
                    $this->DB->query('INSERT INTO '.$this->CACHE_TABLE.' (url, content, mimetype, date) VALUES ("'.addslashes($url).'", "'.addslashes($content).'", "'.addslashes($mimetype).'", '.time().') ON DUPLICATE KEY UPDATE content = "'.addslashes($content).'", date = '.time().', mimetype = "'.addslashes($mimetype).'"');
                }catch(Exception $e){
                    $content = "404";
                }
            }
        }
        return $content;
    }
    
    /**
     * Get content from cache without overwriting it
     * @param string $url url of the content
     * @return string content of the page or "" if unset 
     */
    private function get_no_write($url){
        $content = "";
	$mimetype = "";
	$date = "";
        if ($this->DB != null){
            if (!$this->DB->is_connected()){
                $this->DB->connect();
            }
            $result = $this->DB->query('SELECT content, date, mimetype FROM '.$this->CACHE_TABLE.' WHERE url = "'.addslashes($url).'" AND date > '.(time()-$this->CACHE_TIME).' LIMIT 0,1', false, true);
            if (count($result) > 0){
                $content = $result[0]['content'];
		$mimetype = $result[0]['mimetype'];
		$date = $result[0]['date'];
            }
        }
        return array('content' => $content, 'mimetype' => $mimetype, 'date' => $date);
    }
    
    /**
     * Add url to cache
     * @param string $url url well-formed
     * @param string $urlnocache url with no-cache parameter
     */
    private function set_no_read($url, $urlnocache){
        if ($this->DB != null){
            if (!$this->DB->is_connected()){
                $this->DB->connect();
            }
            $content = "";
            $content = file_get_contents($urlnocache);
	    $headers = get_headers($urlnocache, 1);
	    $mimetype = "";
	    if (array_key_exists('Content-Type', $headers)){
		$mimetype = $headers['Content-Type'];
	    }
            $this->DB->query('INSERT INTO '.$this->CACHE_TABLE.' (url, content, mimetype, date) VALUES ("'.addslashes($url).'", "'.addslashes($content).'", "'.addslashes($mimetype).'", '.time().') ON DUPLICATE KEY UPDATE content = "'.addslashes($content).'", date = '.time().', mimetype = "'.addslashes($mimetype).'"');
        }
    }
    
    /**
     * Force the delete of a page in cache
     * @param string $url URL of the page to delete
     */
    function delete($url){
        if ($this->DB != null){
            if (!$this->DB->is_connected()){
                $this->DB->connect();
            }
            $this->DB->query('DELETE FROM '.$this->CACHE_TABLE.' WHERE url = "'.addslashes($url).'"');
        }
    }
    
    /**
     * Clear the cache table in database 
     */
    function clear_cache(){
        if ($this->DB != null){
            if (!$this->DB->is_connected()){
                $this->DB->connect();
            }
            $this->DB->query('TRUNCATE TABLE '.$this->CACHE_TABLE.'');
        }
    }
    
    /**
     * Return the current url
     * @return string current url
     */
    private function get_current_url(){
        if ($_SERVER['SERVER_PORT'] == 80){
            $protocol = "http://";
        }
        if ($_SERVER['SERVER_PORT'] == 443){
            $protocol = "https://";
        }
        return addslashes($protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    }
    
    /**
     * Add the param no-cache to url
     * @param string $url input url
     * @return string url with no-cache
     */
    private function add_no_cache_param($url){
        $urlnocache = $url;
        if (strrchr ($url, "?") == FALSE){
            $urlnocache .= "?";
        }else{
            $urlnocache .= "&";
        }
        $urlnocache .= "no-cache";
        return $urlnocache;
    }
    
    /**
     * Automated caching start method 
     * @param boolean $skip_caching Disable autocache
     */
    function autocache_start($skip_caching = false){
        if (!$skip_caching && $_SERVER['REQUEST_METHOD'] == 'GET'){
            if (!isset($_GET['no-cache'])){
                $url = $this->get_current_url();
                $contents = $this->get_no_write($url);
                if ($contents['content'] != ""){
		    header('Content-Type: '.$contents['mimetype']);
		    header('Generate-Date: '.$contents['date']);
                    print $contents['content'];
                    $this->DB->disconnect();
                    exit;
                }
            }
        }
    }
    
    /**
     * Automated caching end method 
     * @param boolean $skip_caching Disable autocache
     */
    function autocache_end($skip_caching = false){
        if (!$skip_caching && $_SERVER['REQUEST_METHOD'] == 'GET'){
            if (!isset($_GET['no-cache'])){
                $url = $this->get_current_url();
                $urlnocache = $this->add_no_cache_param($url);
                $this->set_no_read($url, $urlnocache);
                $this->DB->disconnect();
                exit;
            }
        }
    }
    
}

?>
