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
     $this->form_validation->set_rules('pEmail','email','required');
     $this->form_validation->set_rules('pPassword','password','required');

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
    $this->form_validation->set_rules('pEmail','email','required');
    $this->form_validation->set_rules('pPassword','password','required');

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

  function users_get(){
     $sql = "SELECT id_person, concat(name_person,' ', lastname_person) as fullname_person,
     email_person, gender_person,identifier_person,phone_person, role_person,
     IF ((SELECT COUNT(*) FROM tb_users WHERE user_person = id_person) >0, 'Con accesso','Sin accesso') as access_person,
     IF (person_career IS NULL, '',(SELECT name_career FROM tb_careers WHERE person_career = id_career)) as career_person
     FROM tb_persons";
     //$sql = "SELECT * FROM tb_users, tb_persons WHERE id_person = user_person";
     $response  = $this->DAO->sqlQuery($sql);
     $this->response($response,200);
  }
  
  function web_users_post(){
     $this->form_validation->set_data($this->post());
     $this->form_validation->set_rules('pName','Nombre','required');
     $this->form_validation->set_rules('pLastname','Apellido','required');
     $this->form_validation->set_rules('pGender','Genero','required');
     $this->form_validation->set_rules('pEmail','Correo','required|is_unique[tb_persons.email_person]');
     $this->form_validation->set_rules('pIde','Identificador','required|is_unique[tb_persons.identifier_person]');
     $this->form_validation->set_rules('pPhone','Celular','required');

     if($this->form_validation->run()){
      $this->DAO->init_transaction();

        $data_person = array(
           "name_person" => $this->post('pName'),
           "lastname_person" => $this->post('pLastname'),
           "gender_person" => $this->post('pGender'),
           "email_person" => $this->post('pEmail'),
           "identifier_person" => $this->post('pIde'),
           "phone_person" => $this->post('pPhone'),
           "role_person" => 'Almacenista'
        );

        $id_person = $this->DAO->saveOrUpdate('tb_persons',$data_person,array(),TRUE);

        $gen_password = $this->randomPassword();
        $this->load->library('bcrypt');
        $data_user = array(
           "email_user" => $this->post('pEmail'),
           "password_user" => $this->bcrypt->hash_password($gen_password),
           "user_person" => $id_person['data']
        );
        $respuesta = $this->DAO->saveOrUpdate('tb_users',$data_user);

        if($this->DAO->check_transaction()['status'] == "success"){
         $response = array(
            "status"=>"success",
            "message"=>"Creado correctamente",
            "validations"=>array(),
            "data"=>array(
               "key_password" => $gen_password
            )
        );
        }

        else{
         $response = array(
            "status"=>"error",
            "message"=>"Error al crear usuario",
            "validations"=>$this->form_validation->error_array(),
            "data"=>null
        );
        }

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

  function mobile_users_post(){
   $this->form_validation->set_data($this->post());
   $this->form_validation->set_rules('pName','Nombre','required');
   $this->form_validation->set_rules('pLastname','Apellido','required');
   $this->form_validation->set_rules('pGender','Genero','required');
   $this->form_validation->set_rules('pEmail','Correo','required|is_unique[tb_persons.email_person]');
   $this->form_validation->set_rules('pIde','Identificador','required|is_unique[tb_persons.identifier_person]');
   $this->form_validation->set_rules('pPhone','Celular','required');
   $this->form_validation->set_rules('pRole','Rol','required');

   if($this->form_validation->run()){
      if($this->post('pRole') == "Estudiante"){
         $this->form_validation->set_rules('pCareer','Carrera','callback_career_exists');

         if(!$this->form_validation->run()){
            $response = array(
               "status"=>"error",
               "message"=>"Información enviada incorrectamente",
               "validations"=>$this->form_validation->error_array(),
               "data"=>null
              );
              $this->response($response,200);         
            }
         }
            $this->DAO->init_transaction();
      
              $data_person = array(
                 "name_person" => $this->post('pName'),
                 "lastname_person" => $this->post('pLastname'),
                 "gender_person" => $this->post('pGender'),
                 "email_person" => $this->post('pEmail'),
                 "identifier_person" => $this->post('pIde'),
                 "phone_person" => $this->post('pPhone'),
                 "role_person" => $this->post('pRole'),
                 "person_career " => $this->post('pCareer') == "Estudiante" ? $this->post('pCareer') : null
              );
      
              $id_person = $this->DAO->saveOrUpdate('tb_persons',$data_person,array(),TRUE);
      
              $gen_password = $this->randomPassword();
              $this->load->library('bcrypt');
              $data_user = array(
                 "email_user" => $this->post('pEmail'),
                 "password_user" => $this->bcrypt->hash_password($gen_password),
                 "user_person" => $id_person['data']
              );
              $respuesta = $this->DAO->saveOrUpdate('tb_users',$data_user);
      
              if($this->DAO->check_transaction()['status'] == "success"){
               $response = array(
                  "status"=>"success",
                  "message"=>"Creado correctamente",
                  "validations"=>array(),
                  "data"=>array(
                     "key_password" => $gen_password
                  )
              );
              }
      
              else{
               $response = array(
                  "status"=>"error",
                  "message"=>"Error al crear usuario",
                  "validations"=>$this->form_validation->error_array(),
                  "data"=>null
              );
              }
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
