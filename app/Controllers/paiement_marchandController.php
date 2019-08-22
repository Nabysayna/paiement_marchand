<?php

namespace App\Controllers;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use \App\Controller;



class paiement_marchandController extends Controller {
  private $bdd;
  public function __construct(){
    $this->bdd=new \pdo("mysql:host=localhost;dbname=paiement_marchand","root","");
  }
  public function closeConnection(){
    
  }

  public function initier(Request $request, Response $response, $args){
    header("Access-Control-Allow-Origin: *");
    $param = $request->getParsedBody();
    if($request->isPost() && isset($param["service"]) && isset($param["montant"])){
      try{
        $req=$this->bdd->prepare("INSERT INTO transaction(montant,services,etat) VALUES(:montant,:services,:etat)");
        $status=$req->execute(array(":montant"=>$param["montant"],":services"=>$param["service"],":etat"=>1));
        $lastId=$this->bdd->lastInsertId();
        $req->closeCursor();
        $req1=$this->bdd->prepare("SELECT * FROM loumanekh WHERE nomService=:nomService AND etat=1 limit 1");
        $req1->execute(array(":nomService"=>$param["service"]));
        $data=$req1->fetch();
        if($data){
          return $response->withJson(array("idDem"=> $lastId, "codeMessage" =>"#144*211*".$data['codeMessage']."*".$param["montant"]."*2*code#","status"=>$status),200); 
        }else{
           return $response->withJson(array("status"=>false,"message"=>"Pas de numero disponible"));
        }
        
       }catch(Exception $e){
        return $response->withJson(array("status"=>false,"message"=>"problem de connection a la base de donnee"));
      }
    }else{
      return $response->withJson(array("status"=>false,"message"=>"parametres incorrectes"));
    }
     
  } 

  	
  public function confirmer(Request $request, Response $response, $args){
      $param = $request->getParsedBody();
      if($request->isPost() && isset($param["code"]) && isset($param["id"])){
        $req=$this->bdd->prepare("SELECT * FROM transaction WHERE id=:id");
        $req->execute(array(":id"=>intval($param["id"])));
        $data=$req->fetch();
        if($data){
          \date_default_timezone_set('UTC');
          $date=new \DateTime();
          $date=$date->format('Y-m-d H:i');
          $req2=$this->bdd->prepare("UPDATE transaction SET etat=3,codeConfirm=:code,dateValide=:datev WHERE id=:id");
          $rep1=$req2->execute(array(":code"=>$param["code"],":id"=>intval($param["id"]),":datev"=>$date));
          $req2->closeCursor();
          if($rep1==1){
            return $response->withJson(array("code"=>1,"message"=>"transaction confirme"),200);
          }else{
            return $response->withJson(array("code"=>-1,"message"=>"erreur au niveau du serveur"),500);
          }

        }else{
          return $response->withJson(array("code"=>0,"message"=>"transaction non initier"),400);
        }

      }
     /* if($codeSMS == 1234)
        return $response->withJson(array("errorCode"=> 1,"data" => '1234')); 
      else
         return $response->withJson(array("errorCode"=> 0,"data" => '')); 
     */
  } 
    public function ReceptPaiement(Request $request, Response $response, $args){
      $param = $request->getParsedBody();
      if($request->isPost() && isset($param["message"]) && isset($param["service"])){
        ///if($param["service"] == "OrangeMoney"){
          $num = explode(" ", $param["message"]);
          $montant = explode(".", $num[8])[0];
          $numero = $num[10];
        //}
        
        $randomDigit = mt_rand(0, 9);
        $tokenTemp = strval($randomDigit);
        for ($i=1; $i <6 ; $i++) { 
            $randomDigit = mt_rand(0,9);
           $tokenTemp =$tokenTemp.strval($randomDigit) ;
        }
         //$data=$req->fetch();

          $date=new \DateTime();
          $date=$date->format('Y-m-d H:i');
          $req=$this->bdd->prepare("INSERT INTO codeconfirmtab(nomservice,codemessage,datecode,etat) VALUES(:nomservice,:codemessage,:datecode,:etat)");
          $status=$req->execute(array(":nomservice"=>$param["service"],":codemessage"=>$tokenTemp,"datecode"=>$date,":etat"=>1));
         
         if($status==1){
            return $response->withJson(array("message"=>"transaction confirme","numero"=>trim($numero,'.'),"montant"=>$montant,"code"=>$tokenTemp),200);
          }else{
            return $response->withJson(array("code"=>-1,"message"=>"erreur au niveau du serveur"),500);
            
          }


      }
     /* if($codeSMS == 1234)
        return $response->withJson(array("errorCode"=> 1,"data" => '1234')); 
      else
         return $response->withJson(array("errorCode"=> 0,"data" => '')); 
     */
  } 
    
}