<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DAO extends CI_Model {
    function __construct(){
        parent:: __construct();
    }
    function selectEntity($entity,$params =  null,$isUnique = FALSE){
    	if($params){
    		$this->db->where($params);
    	}
    	$query = $this->db->get($entity);
    	if($this->db->error()['message']!=''){
    		$response = array(
    			"status"=>"error",
    			"message"=>$this->db->error()['message'],
                "validations" => array(),
    			"data"=>null
    		);
    	}else{
    		$response = array(
    			"status"=>"success",
    			"message"=>"Información cargada correctamente",
                "validations" => array(),
    			"data"=> $isUnique ?  $query->row() : $query->result()
    		);
    	}
    	return $response;
    }
    function sqlQuery($sql, $params = array(), $isUnique = FALSE){
        $query =  $this->db->query($sql,$params ? $params : null);

        if($this->db->error()['message']!=''){
    		$response = array(
    			"status"=>"error",
    			"message"=>$this->db->error()['message'],
    			"data"=>null
    		);
    	}else{
    		$response = array(
    			"status"=>"success",
    			"message"=>"Información cargada correctamente",
    			"data"=> $isUnique ?  $query->row() : $query->result()
    		);
    	}
    	return $response;
    }

    function deleteEntity($entity,$whereClause =  array()){
        $this->db->where($whereClause);
        $this->db->delete($entity);
        if($this->db->error()['message']!=''){
    		$response = array(
    			"status"=>"error",
    			"message"=>$this->db->error()['message'],
    			"data"=>null
    		);
    	}else{
            $response = array(
    			"status"=>"success",
    			"message"=> "Información borrada correctamente",
    			"data"=>null
    		);
        }
        return $response;
    }

    function saveOrUpdate($entity,$data,$whereClause = null, $returnKey = FALSE){
    	if($whereClause){
    		$this->db->where($whereClause);
            $this->db->update($entity,$data);
    	}else{
        $this->db->insert($entity,$data);
      }
    	if($this->db->error()['message']!=''){
    		$response = array(
    			"status"=>"error",
    			"message"=>$this->db->error()['message'],
    			"data"=>null
    		);
    	}else{
        if($whereClause){
            $msg = "Información actualizada correctamente!";
        }else{
            $msg = "Información registrada correctamente!";
        }
    		$response = array(
    			"status" => "success",
    			"message" => $msg,
          "data" => null
    		);
        if($returnKey){
          $response['data'] = $this->db->insert_id();
        }
    	}
    	return $response;
    }

    function login($email,$password, $app = "web"){
        $this->db->where('correo_usuario',$email);
        $usuario_existe = $this->db->get('usuario')->row();
        if($usuario_existe){
            if($usuario_existe->password_usuario == $password){
                $has_permition = TRUE;
                if($app == "mobile"){
                    $roles_permited = array('Profesor');
                    if(!in_array($usuario_existe->privilegios_usuario, $roles_permited)){
                        $has_permition = FALSE;
                        $response = array(
                        "status" => "error",
                        "message" => "Por el momento, los Estudiantes no tienen acceso a la aplicación. Disculpe las molestias",
                        "validations" => array(),
                        "data" => null
                        );
                    }
                }
                if($has_permition){

                return array(
                    "status" => "success",
                    "message" => "Usuario cargado correctamente.",
                    "data" => array(
                        "nombre_usuario" => $usuario_existe->nombre_usuario,
                        "correo_usuario" => $usuario_existe->correo_usuario,
                        "apellidos_usuario" => $usuario_existe->apellido1_usuario.' '.$usuario_existe->apellido2_usuario
                        )
                    );
                }

            }else{
                $response = array(
                    "status" => "error",
                    "message" => "La clave ingresada no es correcta.",
                    "validations" => array(),
                    "data" => null
                    );
            }
        }else{
            $response = array(
                "status" => "error",
                "message" => "El correo ingresado no existe.",
                "validations" => array(),
                "data" => null
            );
        }
        return $response;
    }



    

    function init_transaction(){
        $this->db->trans_begin();
    }

    function check_transaction(){
        if($this->db->trans_status()){
            $this->db->trans_commit();
            $response = array(
                "status" => "success",
                "message" => "Proceso completado correctamente",
                "data" => null
            );
        }else{
            $this->db->trans_rollback();
            $response = array(
                "status" => "error",
                "message" => "Error al completar el proceso",
                "data" => null
            );
        }
        return $response;
    }



}