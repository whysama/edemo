<?php
/**
 * Outil de redirection de trafic vers des pages prédéfinies selon le user agent
 */
class MobileRedirect {

  private $redirections;
  private $defaultUri = '';

  public $androidMarketUri = 'https://play.google.com/store/apps/details?id=';
  public $appStoreUri = 'https://itunes.apple.com/app/id';

  function __construct() {
    $this->redirections = array();;
  }

  /**
   * Url de redirection par defaut
   * @param type $uri
   */
  function setDefaultUri($uri){
    $this->defaultUri = $uri;
  }

  /**
   * Ajout de redirection personnalisé
   * @param type $userAgentExtract extrait du User Agent
   * @param type $uri Url de redirection
   * @return boolean
   */
  function addRedirection($userAgentExtract, $uri){
    if (!array_key_exists($userAgentExtract, $this->redirections)){
      $this->redirections[$userAgentExtract] = $uri;
      return true;
    }
    return false;
  }

  /**
   * Ecoute des requetes pour redirection
   */
  function listen(){
    if (isset($_SERVER['HTTP_USER_AGENT'])){
      $ua = $_SERVER['HTTP_USER_AGENT'];
    }else{
      $ua = '';
    }
    foreach ($this->redirections as $uaPart => $uri) {
      if (stristr($ua, $uaPart)){
        header('Location:'.$uri, true, 302);
        exit(0);
      }
    }
    if ($this->defaultUri != ''){
      header('Location:'.$this->defaultUri, true, 302);
    }else{
      echo 'User-Agent unsupported.';
    }

  }

  /**
   * Redirection vers google play
   * @param type $appId identifiant de l'application google
   * @return boolean
   */
  function setAndroidMarketRedirection($appId){
    return $this->addRedirection('android', $this->androidMarketUri.$appId);
  }

  /**
   * Redirection vers app store
   * @param type $appId identifiant de l'application apple (sans id)
   * @return boolean
   */
  function setAppStoreRedirection($appId){
    return ($this->addRedirection('iphone', $this->appStoreUri.$appId) && $this->addRedirection('ipad', $this->appStoreUri.$appId));
  }



}