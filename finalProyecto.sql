CREATE DATABASE wolfledge;
USE wolfledge;


CREATE TABLE usuario (
    id_usuario INT primary key auto_increment,
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
    nombre_especialidad VARCHAR(80) not null UNIQUE
);

CREATE TABLE especialidad_profesor (
    id_espro int primary key auto_increment,
    usuariofk int,
    FOREIGN KEY (usuariofk) REFERENCES usuario(id_usuario),
    especialidadfk int,
    FOREIGN KEY (especialidadfk) REFERENCES especialidad(id_especialidad)
);


CREATE TABLE material (
    id_material int primary key auto_increment,
    link_clase VARCHAR(80) not null
);

CREATE TABLE clase (
    id_clase int primary key auto_increment,
    descripcion_clase text not null,
    horario_inicio_clase TIME not null,
    horario_fin_clase TIME not null,
    fecha_inicio DATE not null,
    fecha_fin DATE not null,
    especialidadfk int,
    FOREIGN KEY (especialidadfk) REFERENCES especialidad(id_especialidad),
    materialfk int,
    FOREIGN KEY (materialfk) REFERENCES material(id_material),
    usuariofk int,
    FOREIGN KEY (usuariofk) REFERENCES usuario(id_usuario)
);

CREATE TABLE alumno_clase (
    id_alumno_clase int primary key auto_increment,
    clasefk int,
    FOREIGN KEY(clasefk) REFERENCES clase (id_clase),
    usuariofk int,
    FOREIGN KEY (usuariofk) REFERENCES usuario (id_usuario)
);


