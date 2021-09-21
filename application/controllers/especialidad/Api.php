<?php
require APPPATH . 'core/MY_RootController.php';

defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends MY_RootController {
    function __construct(){
        parent:: __construct();
        $this->load->model('DAO');
    }


   function especialidad_get(){
       if($this->get('eId')){
            $response = $this->DAO->selectEntity('especialidad',array('id_especialidad' => $this->get('eId')),TRUE);
        }else{
            $response = $this->DAO->selectEntity('especialidad');
        }
        $this->response($response,200);
    }

    function especialidad_post(){
        $this->form_validation->set_data($this->post());
        $this->form_validation->set_rules('pName','Especialidad','required|is_unique[especialidad.nombre_especialidad]');

        if($this->form_validation->run()){
      
              $data_person = array(
                 "nombre_especialidad" => $this->post('pName')
              );
      
              $response = $this->DAO->saveOrUpdate('especialidad',$data_person,array(),TRUE);
      
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

    function especialidad_put(){
        if($this->get('eId')){
          $especialidad_existe =  $this->DAO->selectEntity('especialidad',array('id_especialidad' => $this->get('eId')),TRUE);
          if($especialidad_existe){
              $this->form_validation->set_data($this->put());
              $this->form_validation->set_rules('pName','Especialidad','required|is_unique[especialidad.nombre_especialidad]');

              if($this->form_validation->run()){
                  $data = array(
                    "nombre_especialidad" => $this->put('pName')
                    );
                  $response = $this->DAO->saveOrUpdate('especialidad',$data,array('id_especialidad' => $this->get('eId')));
                  
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

      function especialidad_delete(){
        if($this->get('eId')){
            $especialidad_existe = $this->DAO->selectEntity('especialidad',array('id_especialidad'=>$this->get('eId')),TRUE);
            if($especialidad_existe){
                $this->DAO->deleteEntity('especialidad',array('id_especialidad'=>$this->get('eId')),TRUE);
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


      function especialidad_profesor_get(){
          $sql = "SELECT concat(nombre_usuario,' ',apellido1_usuario,' ',apellido2_usuario) as Nombre_Profesor, nombre_especialidad as especialidad from especialidad_profesor
          JOIN usuario on especialidad_profesor.usuariofk = usuario. id_usuario
          JOIN especialidad on especialidad_profesor.especialidadfk = especialidad.id_especialidad;";
          $response = $this->DAO->sqlQuery($sql);
          $this->response($response,200);
      }


      function especialidad_profesor_post(){
        $this->form_validation->set_data($this->post());
        $this->form_validation->set_rules('pName','Profesor','required');
        $this->form_validation->set_rules('pEsp','Especialidad','required');

        if($this->form_validation->run()){
      
              $data_person = array(
                 "usuariofk" => $this->post('pName'),
                 "especialidadfk" => $this->post('pEsp')
              );
      
              $response = $this->DAO->saveOrUpdate('especialidad_profesor',$data_person,array(),TRUE);
      
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






}