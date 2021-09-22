<?php
require APPPATH . 'core/MY_RootController.php';

defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends MY_RootController {
    function __construct(){
        parent:: __construct();
        $this->load->model('DAO');
    }


   function material_get(){
       if($this->get('mId')){
            $response = $this->DAO->selectEntity('material',array('id_material' => $this->get('mId')),TRUE);
        }else{
            $response = $this->DAO->selectEntity('material');
        }
        $this->response($response,200);
    }

    function material_post(){
        $this->form_validation->set_data($this->post());
        $this->form_validation->set_rules('pName','Material','required');

        if($this->form_validation->run()){
      
              $data_person = array(
                 "link_clase" => $this->post('pName')
              );
      
              $response = $this->DAO->saveOrUpdate('material',$data_person,array(),TRUE);
      
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

    function material_put(){
        if($this->get('mId')){
          $especialidad_existe =  $this->DAO->selectEntity('material',array('id_material' => $this->get('mId')),TRUE);
          if($especialidad_existe){
              $this->form_validation->set_data($this->put());
              $this->form_validation->set_rules('pName','Material','required');

              if($this->form_validation->run()){
                  $data = array(
                    "link_clase" => $this->put('pName')
                    );
                  $response = $this->DAO->saveOrUpdate('material',$data,array('id_material' => $this->get('mId')));
                  
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

      function material_delete(){
        if($this->get('mId')){
            $especialidad_existe = $this->DAO->selectEntity('material',array('id_material'=>$this->get('mId')),TRUE);
            if($especialidad_existe){
                $this->DAO->deleteEntity('material',array('id_material'=>$this->get('mId')),TRUE);
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

}