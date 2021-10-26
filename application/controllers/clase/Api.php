<?php
require APPPATH . 'core/MY_RootController.php';

defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends MY_RootController {
    function __construct(){
        parent:: __construct();
        $this->load->model('DAO');
    }


   function clase_get(){
       if($this->get('cId')){
            $response = $this->DAO->selectEntity('clase_view',array('id_clase' => $this->get('cId')),TRUE);
        }else{
            $response = $this->DAO->selectEntity('clase_view',array('estatus_clase'=>'Activo'));
        }
        $this->response($response,200);
    }

    function profesores_get(){
        if($this->get('pId')){
            $response = $this->DAO->selectEntity('usuario',array('id_usuario' => $this->get('pId'),'privilegios_usuario' => 'Profesor'),TRUE);
        }else{
            $response = $this->DAO->selectEntity('usuario',array('estatus_usuario'=>'Activo','privilegios_usuario' => 'Profesor'));
        }
        $this->response($response,200);
    }

    function clase_post(){
        $this->form_validation->set_data($this->post());
        $this->form_validation->set_rules('pDesc','Descripción','required');
        $this->form_validation->set_rules('pHoraInicio','Horario de Inicio','required');
        $this->form_validation->set_rules('pHoraFinal','Horario de Fin','required');
        $this->form_validation->set_rules('pFechaInicio','Fecha de Inicio','required');
        $this->form_validation->set_rules('pFechaFin','Fecha de Fin','required');
        $this->form_validation->set_rules('pEsp','Especialidad','required|callback_especialidad_exists');
        $this->form_validation->set_rules('pMaterial','Material','required|callback_material_exists');
        $this->form_validation->set_rules('pProfe','Profesor','required|callback_usuario_exists');


        if($this->form_validation->run()){
      
              $data_person = array(
                 "descripcion_clase" => $this->post('pDesc'),
                 "horario_inicio_clase" => $this->post('pHoraInicio'),
                 "horario_fin_clase" => $this->post('pHoraFinal'),
                 "fecha_inicio" => $this->post('pFechaInicio'),
                 "fecha_fin" => $this->post('pFechaFin'),
                 "especialidadfk" => $this->post('pEsp'),
                 "materialfk" => $this->post('pMaterial'),
                 "usuariofk" => $this->post('pProfe')

              );
      
              $response = $this->DAO->saveOrUpdate('clase',$data_person,array(),TRUE);
      
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

    function clase_put(){
        if($this->get('cId')){
            $especialidad_profesor_existe =  $this->DAO->selectEntity('clase',array('id_clase' => $this->get('cId')),TRUE);
            if($especialidad_profesor_existe){
                $this->form_validation->set_data($this->put());
                $this->form_validation->set_rules('pDesc','Descripción','required');
                $this->form_validation->set_rules('pHoraInicio','Horario de Inicio','required');
                $this->form_validation->set_rules('pHoraFinal','Horario de Fin','required');
                $this->form_validation->set_rules('pFechaInicio','Fecha de Inicio','required');
                $this->form_validation->set_rules('pFechaFin','Fecha de Fin','required');
                $this->form_validation->set_rules('pEsp','Especialidad','required|callback_especialidad_exists');
                $this->form_validation->set_rules('pMaterial','Material','required|callback_material_exists');
                $this->form_validation->set_rules('pProfe','Profesor','required|callback_usuario_exists');

                if($this->form_validation->run()){
                    $data = array(
                        "descripcion_clase" => $this->put('pDesc'),
                        "horario_inicio_clase" => $this->put('pHoraInicio'),
                        "horario_fin_clase" => $this->put('pHoraFinal'),
                        "fecha_inicio" => $this->put('pFechaInicio'),
                        "fecha_fin" => $this->put('pFechaFin'),
                        "especialidadfk" => $this->put('pEsp'),
                        "materialfk" => $this->put('pMaterial'),
                        "usuariofk" => $this->put('pProfe')
                    );
                    $response = $this->DAO->saveOrUpdate('clase',$data,array('id_clase' => $this->get('cId')));
                    
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

      function clase_delete(){
        if($this->get('cId')){
            $especialidad_existe = $this->DAO->selectEntity('clase',array('id_clase'=>$this->get('cId')),TRUE);
            if($especialidad_existe){
                $this->DAO->deleteEntity('especialidad',array('id_clase'=>$this->get('cId')),TRUE);
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


    function usuario_exists($value){
        $usuario_exists = $this->DAO->selectEntity('usuario',array('id_usuario' => $value, 'estatus_usuario' => 'Activo', 'privilegios_usuario' => 'Profesor'),TRUE);
        if($usuario_exists['data']){
            return TRUE;
     
        }else{
            $this->form_validation->set_message('usuario_exists','El campo {field} no tiene permisos para crear clase');
            return False;
     
        }
    }

    function especialidad_exists($value){
        $especialidad_exists = $this->DAO->selectEntity('especialidad',array('id_especialidad' => $value),TRUE);
        if($especialidad_exists['data']){
            return True;
        }else{
            $this->form_validation->set_message('especialidad_exists','El campo {field} no existe en la base de datos');
            return False;
        }
    }

    function material_exists($value){
        $material_exists = $this->DAO->selectEntity('material',array('id_material' => $value),TRUE);
        if($material_exists['data']){
            return True;
        }else{
            $this->form_validation->set_message('material_exists','El campo {field} no existe en la base de datos');
            return False;
        }
    }



    function clase_alumno_get(){
        $sql = "SELECT concat(nombre_usuario,' ',apellido1_usuario,' ',apellido2_usuario) as Nombre_Alumno, nombre_especialidad from alumno_clase
        Left JOIN usuario on alumno_clase.usuariofk = usuario.id_usuario
        JOIN clase on alumno_clase.clasefk = clase.id_clase
        JOIN especialidad on clase.especialidadfk = especialidad.id_especialidad;";
        $response = $this->DAO->sqlQuery($sql);
        $this->response($response,200);
    }


    function clase_alumno_post(){
        $this->form_validation->set_data($this->post());
        $this->form_validation->set_rules('pClase','Clase','required|callback_clase_exists');
        $this->form_validation->set_rules('pAlumno','Alumno','required|is_unique[alumno_clase.usuariofk]|callback_alumno_exists');


        if($this->form_validation->run()){
      
              $data_person = array(
                 "clasefk" => $this->post('pClase'),
                 "usuariofk" => $this->post('pAlumno')

              );
      
              $response = $this->DAO->saveOrUpdate('alumno_clase',$data_person,array(),TRUE);
      
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


    function clase_alumno_put(){
        if($this->get('caId')){
            $especialidad_profesor_existe =  $this->DAO->selectEntity('alumno_clase',array('id_alumno_clase' => $this->get('caId')),TRUE);
            if($especialidad_profesor_existe){
                $this->form_validation->set_data($this->put());
                $this->form_validation->set_rules('pClase','Clase','required|callback_clase_exists');
                $this->form_validation->set_rules('pAlumno','Alumno','required|is_unique[alumno_clase.usuariofk]|callback_alumno_exists');

                if($this->form_validation->run()){
                    $data = array(
                        "clasefk" => $this->put('pClase'),
                        "usuariofk" => $this->put('pAlumno')
                    );
                    $response = $this->DAO->saveOrUpdate('alumno_clase',$data,array('id_alumno_clase' => $this->get('caId')));
                    
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

    function clase_alumno_delete(){
        if($this->get('caId')){
            $especialidad_existe = $this->DAO->selectEntity('alumno_clase',array('id_alumno_clase'=>$this->get('caId')),TRUE);
            if($especialidad_existe){
                $this->DAO->deleteEntity('alumno_clase',array('id_alumno_clase'=>$this->get('caId')),TRUE);
            }else{
                $response = array(
                    "status"=>"error",
                    "message"=>"La clave no fue encontrada en la base de datos",
                    "validations"=>$this->form_validation->error_array(),
                    "data"=>null
                );
                $this->response($response,200);
              }
          }else{
            $response = array(
                "status"=>"error",
                "message"=>"Id no enviado",
                "validations"=>$this->form_validation->error_array(),
                "data"=>null
            );
            $this->response($response,200);
          }
    }

    function alumno_exists($value){
        $alumno_exists = $this->DAO->selectEntity('usuario',array('id_usuario' => $value, 'estatus_usuario' => 'Activo'),TRUE);
        if($alumno_exists['data']){
            return TRUE;
     
        }else{
            $this->form_validation->set_message('alumno_exists','El campo {field} no existe en la base de datos');
            return False;
     
        }
    }
    function clase_exists($value){
        $clase_exists = $this->DAO->selectEntity('clase',array('id_clase' => $value, 'estatus_clase' => 'Activo'),TRUE);
        if($clase_exists['data']){
            return TRUE;
     
        }else{
            $this->form_validation->set_message('clase_exists','El campo {field} no existe en la base de datos ó fue dada de baja');
            return False;
     
        }
    }

}