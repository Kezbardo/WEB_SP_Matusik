<?php

class MySessions
{
    /**
     *  Pri vytvoreni objektu je zahajena session.
     */
    public function __construct(){
        session_start(); // zahajim
    }

    /**
     *  Funkce pro ulozeni hodnoty do session.
     *  @param string $key     Klic do pole session (jmeno pro session).
     *  @param mixed $value    Hodnota
     */
    public function setSession(string $key, $value){
        $_SESSION[$key] = $value;
    }

    /**
     *  Je session nastavena?
     *  @param string $key     Klic do pole session.
     *  @return bool
     */
    public function isSessionSet(string $key):bool {
        return isset($_SESSION[$key]);
    }

    /**
     *  Vrati hodnotu dane session nebo null, pokud session neni nastavena.
     *  @param string $key     Klic do pole session.
     *  @return mixed|null
     */
    public function readSession(string $key){
        // existuje dany atribut v session
        if($this->isSessionSet($key)){
            return $_SESSION[$key];
        } else {
            return null;
        }
    }

    /**
     *  Odstrani danou session.
     *  @param string $key     Klic do pole session.
     */
    public function removeSession(string $key){
        unset($_SESSION[$key]);
    }

    /**
     * Vyprazdni cele pole $_SESSION.
     */
    public function removeAllSessions(){
        session_unset();
    }

}
?>