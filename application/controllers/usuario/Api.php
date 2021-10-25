<?php
require APPPATH . 'core/MY_RootController.php';

defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends MY_RootController {
    function __construct(){
        parent:: __construct();
        $this->load->model('DAO');
    }

   function login_post(){
     $this->form_validation->set_data($this->post());
     $this->form_validation->set_rules('pEmail','Email','required');
     $this->form_validation->set_rules('pPassword','Password','required');

     if($this->form_validation->run()){
        $response = $this->DAO->login($this->post('pEmail'),$this->post('pPassword'));

     }else{
        $response = array(
            "status"=>"error",
            "message"=>"Información enviada incorrectamente.",
            "validations"=>$this->form_validation->error_array(),
            "data"=>null
        );
     }
     $this->response($response,200);
   }

   function login_mobile_post(){
    $this->form_validation->set_data($this->post());
    $this->form_validation->set_rules('pEmail','Email','required');
    $this->form_validation->set_rules('pPassword','Password','required');

    if($this->form_validation->run()){
       $response = $this->DAO->login($this->post('pEmail'),$this->post('pPassword'),"mobile");

    }else{
       $response = array(
           "status"=>"error",
           "message"=>"Información enviada incorrectamente.",
           "validations"=>$this->form_validation->error_array(),
           "data"=>null
       );
    }
    $this->response($response,200);
  }

   function usuario_get(){
       if($this->get('uId')){
            $response = $this->DAO->selectEntity('usuario',array('id_usuario' => $this->get('uId')),TRUE);
        }else{
            $response = $this->DAO->selectEntity('usuario');
        }
        $this->response($response,200);
    }

    function usuario_post(){
        $this->form_validation->set_data($this->post());
        $this->form_validation->set_rules('pName','Nombre','required');
        $this->form_validation->set_rules('pApellido1','Apellido Paterno','required');
        $this->form_validation->set_rules('pApellido2','Apellido Materno','required');
        $this->form_validation->set_rules('pEdad','Edad','required');
        $this->form_validation->set_rules('pPais','Pais','required');
        $this->form_validation->set_rules('pEmail','Email','required|is_unique[usuario.correo_usuario]');
        $this->form_validation->set_rules('pPassword','Password','required');
        $this->form_validation->set_rules('pPrivilegios','Privilegios','required');

        if($this->form_validation->run()){
      
              $data_person = array(
                 "nombre_usuario" => $this->post('pName'),
                 "apellido1_usuario" => $this->post('pApellido1'),
                 "apellido2_usuario" => $this->post('pApellido2'),
                 "edad_usuario" => $this->post('pEdad'),
                 "pais_usuario" => $this->post('pPais'),
                 "correo_usuario" => $this->post('pEmail'),
                 "password_usuario" => $this->post('pPassword'),
                 "privilegios_usuario" => $this->post('pPrivilegios')
              );
      
              $response = $this->DAO->saveOrUpdate('usuario',$data_person,array(),TRUE);
      
           }else{
              $response = array(
               "status"=>"error",
               "message"=>"Información enviada incorrectamente",
               "validations"=>$this->form_validation->error_array(),
               "data"=>null
              );
           }
         $this->response($response,200);
    }

    function usuario_put(){
        if($this->get('uId')){
          $usuario_existe =  $this->DAO->selectEntity('usuario',array('id_usuario' => $this->get('uId')),TRUE);
          if($usuario_existe){
              $this->form_validation->set_data($this->put());
              $this->form_validation->set_rules('pName','Nombre','required');
              $this->form_validation->set_rules('pApellido1','Apellido Paterno','required');
              $this->form_validation->set_rules('pApellido2','Apellido Materno','required');
              $this->form_validation->set_rules('pEdad','Edad','required');
              $this->form_validation->set_rules('pPais','Pais','required');
              $this->form_validation->set_rules('pEmail','Email','required');
              $this->form_validation->set_rules('pPassword','Password','required');
              $this->form_validation->set_rules('pPrivilegios','Privilegios','required');
              if($this->form_validation->run()){
                  $data = array(
                    "nombre_usuario" => $this->put('pName'),
                    "apellido1_usuario" => $this->put('pApellido1'),
                    "apellido2_usuario" => $this->put('pApellido2'),
                    "edad_usuario" => $this->put('pEdad'),
                    "pais_usuario" => $this->put('pPais'),
                    "correo_usuario" => $this->put('pEmail'),
                    "password_usuario" => $this->put('pPassword'),
                    "privilegios_usuario" => $this->put('pPrivilegios')
                    );
                  $response = $this->DAO->saveOrUpdate('usuario',$data,array('id_usuario' => $this->get('uId')));
                  
              }else{
                 $response = array(
                     "status"=>"success",
                     "message"=>"Información enviada incorrectamente.",
                     "validations"=>$this->form_validation->error_array(),
                     "data"=>null
                 );
              }
          }else{
              $response = array(
                  "status"=>"error",
                  "message"=>"Id no enviado",
                  "validations"=>$this->form_validation->error_array(),
                  "data"=>null
              );
            }
            $this->response($response,200);
          }
      }

      function usuario_delete(){
        if($this->get('uId')){
            $usuario_existe = $this->DAO->selectEntity('usuario',array('id_usuario'=>$this->get('uId')),TRUE);
            if($usuario_existe){
                $this->DAO->deleteEntity('usuario',array('id_usuario'=>$this->get('uId')),TRUE);
            }else{
                $response = array(
                    "status"=>"error",
                    "message"=>"La clave no fue encontrada en la base de datos",
                    "validations"=>$this->form_validation->error_array(),
                    "data"=>null
                );
              }
          }else{
            $response = array(
                "status"=>"error",
                "message"=>"Id no enviado",
                "validations"=>$this->form_validation->error_array(),
                "data"=>null
            );
          }
          
      }



  function career_exists($value){
   $career_exists = $this->DAO->selectEntity('tb_careers',array('id_career' => $value, 'status_career' => 'Activo'),TRUE);
   if($career_exists['data']){
       return TRUE;

   }else{
       $this->form_validation->set_message('career_exists','El campo {field} no existe en la base de datos');
       return False;

   }
  }



}