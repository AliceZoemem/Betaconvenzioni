<?php

class user {
    
    public $nome;
    public $cognome;
    public $email;
    protected $password;
    
    public function registra($nome,$cognome,$email,$password)
    {
        $this->nome = $nome;
        $this->cognome = $cognome;
        $this->email = $email;
        $this->password = $password;
    }
    
    public function getEmail()
    {
        return $this->email;
    }
    
    public function getNome()
    {
        return $this->nome;
    }
    
    public function getCognome()
    {
        return $this->cognome;
    }
    
    public function getCoordinate($address) 
    {
    
    $address = urlencode($address);
    $url = "http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false";
    $ch = curl_init();
    $options = array(
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL            => $url,
        CURLOPT_HEADER         => false,
    );
        
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    curl_close($ch);
    if (!$response) {
        return false;
    }
    $response = json_decode($response);
    if ($response->status !== 'OK') {
        return false;
    }
    $lat  = $response->results[0]->geometry->location->lat;
    $long = $response->results[0]->geometry->location->lng;
    return "$lat|$long";
    }
}
