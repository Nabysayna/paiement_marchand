<?php

namespace App\Controllers;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use \App\Controller;



class paiement_marchandController extends Controller {
  private $bdd;
  public function __construct(){
    $this->bdd=new \pdo("mysql:host=localhost;dbname=mbirmiprod","root","");
  }
  public function closeConnection(){
    $this->bdd=null;
  }

  public function initier(Request $request, Response $response, $args){
    header("Access-Control-Allow-Origin: *");
    $param = $request->getParsedBody();
    if(isset($param["service"]) && isset($param["montant"])){
      try{
       // $bdd=new \pdo("mysql:host=localhost;dbname=mbirmiprod","root","");
        $req=$this->bdd->prepare("INSERT INTO transaction(montant,services,etat) VALUES(:montant,:services,:etat)");
        $status=$req->execute(array(":montant"=>$param["montant"],":services"=>$param["service"],":etat"=>1));
       // closeConnection();
        return $response->withJson(array("idDem"=> 1, "codeMessage" =>"#123#","status"=>$status),200); 
      }catch(Exception $e){
       // closeConnection();
        return $response->withJson(array("status"=>false,"message"=>"problem de connection a la base de donnee"));
      }
    }else{
     // closeConnection();
      return $response->withJson(array("status"=>false,"message"=>"parametres incorrectes"));
    }
     
  } 

  	
  public function confirmer(Request $request, Response $response, $args){
      $codeSMS = $request->getParsedBody()["codeSMS"];
      if($codeSMS == 1234)
        return $response->withJson(array("errorCode"=> 1,"data" => '1234')); 
      else
         return $response->withJson(array("errorCode"=> 0,"data" => '')); 
     
  } 
    
    
}