<?php

namespace App\Controllers;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use \App\Controller;



class paiement_marchandController extends Controller {

  public function initier(Request $request, Response $response, $args){
    header("Access-Control-Allow-Origin: *");
    $service = $request->getParsedBody()["service"];
      return $response->withJson(array("idDem"=> 1, "codeMessage" =>"#123#")); 
  } 

  	
  public function confirmer(Request $request, Response $response, $args){
      $codeSMS = $request->getParsedBody()["codeSMS"];
      if($codeSMS == 1234)
        return $response->withJson(array("errorCode"=> 1,"data" => '1234')); 
      else
         return $response->withJson(array("errorCode"=> 0,"data" => '')); 
     
  } 
    
    
}