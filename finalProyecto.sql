CREATE DATABASE wolfledge;
USE wolfledge;


CREATE TABLE usuario (
    id_usuario INT primary key auto_increment,
    imagen_usuario VARCHAR(180),
    nombre_usuario varchar(80) not null,
    apellido1_usuario varchar(80) not null,
    apellido2_usuario varchar(80),
    edad_usuario int not null,
    pais_usuario varchar(80) not null,
    correo_usuario VARCHAR(80) not null UNIQUE,
    password_usuario VARCHAR(80) not null,
    privilegios_usuario enum('Estudiante','Profesor','Administrador') DEFAULT 'Estudiante',
    estatus_usuario enum('Activo','Inactivo') DEFAULT 'Activo',
    creacion_usuario TIMESTAMP NOT NULL DEFAULT current_timestamp(),
    update_usuario timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
);

CREATE TABLE especialidad(
    id_especialidad int primary key auto_increment,
    nombre_especialidad VARCHAR(80) not null UNIQUE,
    estatus_especialidad enum('Activo','Inactivo') DEFAULT 'Activo'
); -- normal --

CREATE TABLE especialidad_profesor (
    id_espro int primary key auto_increment,
    usuariofk int,
    FOREIGN KEY (usuariofk) REFERENCES usuario(id_usuario),
    especialidadfk int,
    FOREIGN KEY (especialidadfk) REFERENCES especialidad(id_especialidad)
); -- foreign --


CREATE TABLE material (
    id_material int primary key auto_increment,
    link_clase VARCHAR(80) not null,
    estatus_material enum('Activo','Inactivo') DEFAULT 'Activo'
); -- normal --

CREATE TABLE clase (
    id_clase int primary key auto_increment,
    descripcion_clase text not null,
    horario_inicio_clase TIME not null,
    horario_fin_clase TIME not null,
    fecha_inicio DATE not null,
    fecha_fin DATE not null,
    especialidadfk int,
    estatus_clase enum('Activo','Inactivo') DEFAULT 'Activo',
    FOREIGN KEY (especialidadfk) REFERENCES especialidad(id_especialidad),
    materialfk int,
    FOREIGN KEY (materialfk) REFERENCES material(id_material),
    usuariofk int,
    FOREIGN KEY (usuariofk) REFERENCES usuario(id_usuario)
); -- foreign --

CREATE TABLE alumno_clase (
    id_alumno_clase int primary key auto_increment,
    clasefk int,
    FOREIGN KEY(clasefk) REFERENCES clase (id_clase),
    usuariofk int,
    FOREIGN KEY (usuariofk) REFERENCES usuario (id_usuario)
);  -- foreign --


CREATE VIEW usuario_view AS SELECT 
id_usuario,nombre_usuario,apellido1_usuario,apellido2_usuario
,edad_usuario,pais_usuario,correo_usuario,password_usuario,privilegios_usuario,estatus_usuario from usuario;


CREATE VIEW especialidad_view AS SELECT id_espro, id_usuario,  concat(nombre_usuario,' ',apellido1_usuario,' ',apellido2_usuario) as Nombre_Profesor, id_especialidad,nombre_especialidad as especialidad from especialidad_profesor
JOIN usuario on especialidad_profesor.usuariofk = usuario.id_usuario
JOIN especialidad on especialidad_profesor.especialidadfk = especialidad.id_especialidad;

-- SELECT concat(nombre_usuario,' ',apellido1_usuario,' ',apellido2_usuario) as Nombre_Alumno, nombre_especialidad from alumno_clase
-- Left JOIN usuario on alumno_clase.usuariofk = usuario.id_usuario
-- JOIN clase on alumno_clase.clasefk = clase.id_clase
-- JOIN especialidad on clase.especialidadfk = especialidad.id_especialidad;


SELECT descripcion_clase, nombre_usuario, nombre_especialidad from clase
JOIN usuario on clase.usuariofk = usuario.id_usuario
JOIN especialidad on especialidad_profesor.especialidadfk = especialidad.id_especialidad;

CREATE VIEW clase_view AS SELECT id_clase,id_usuario,id_especialidad ,descripcion_clase, horario_inicio_clase,horario_fin_clase,
fecha_inicio,fecha_fin,estatus_clase,concat(nombre_usuario,' ',apellido1_usuario,' ',apellido2_usuario) as Nombre_Profesor,link_clase, nombre_especialidad from clase
JOIN usuario on clase.usuariofk = usuario.id_usuario
JOIN material on clase.materialfk = material.id_material
JOIN especialidad on clase.especialidadfk = especialidad.id_especialidad;