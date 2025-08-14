DELIMITER $$
--Listar mentores
CREATE OR REPLACE PROCEDURE sp_mentor_list()
BEGIN
    SELECT 
        p.id, p.nombre, p.email, p.nivel_habilidad,
        m.especialidad, m.experiencia, m.disponibilidad_horaria
    FROM MentorTecnico m
    JOIN Participante p ON m.mentor_id = p.id
    ORDER BY p.nombre ASC;
END$$

-- Buscar mentor por ID
CREATE OR REPLACE PROCEDURE sp_find_mentor(IN p_id INT)
BEGIN
    SELECT 
        p.id, p.nombre, p.email, p.nivel_habilidad,
        m.especialidad, m.experiencia, m.disponibilidad_horaria
    FROM MentorTecnico m
    JOIN Participante p ON m.mentor_id = p.id
    WHERE p.id = p_id;
END$$

-- Crear mentor
CREATE OR REPLACE PROCEDURE sp_create_mentor(
    IN p_nombre VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_nivel_habilidad VARCHAR(50),
    IN p_especialidad VARCHAR(100),
    IN p_experiencia INT,
    IN p_disponibilidad_horaria INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION ROLLBACK;

    START TRANSACTION;

    INSERT INTO Participante (nombre, email, nivel_habilidad, tipo)
    VALUES (p_nombre, p_email, p_nivel_habilidad, 'MentorTecnico');

    SET @new_id := LAST_INSERT_ID();

    INSERT INTO MentorTecnico (mentor_id, especialidad, experiencia, disponibilidad_horaria)
    VALUES (@new_id, p_especialidad, p_experiencia, p_disponibilidad_horaria);

    COMMIT;
    SELECT @new_id AS mentor_id;
END$$

-- Actualizar mentor
CREATE OR REPLACE PROCEDURE sp_update_mentor(
    IN p_id INT,
    IN p_nombre VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_nivel_habilidad VARCHAR(50),
    IN p_especialidad VARCHAR(100),
    IN p_experiencia INT,
    IN p_disponibilidad_horaria INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION ROLLBACK;

    START TRANSACTION;

    UPDATE Participante
    SET nombre = p_nombre, email = p_email, nivel_habilidad = p_nivel_habilidad
    WHERE id = p_id;

    UPDATE MentorTecnico
    SET especialidad = p_especialidad, experiencia = p_experiencia, disponibilidad_horaria = p_disponibilidad_horaria
    WHERE mentor_id = p_id;

    COMMIT;
END$$

-- Eliminar mentor
CREATE OR REPLACE PROCEDURE sp_delete_mentor(IN p_id INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION ROLLBACK;

    START TRANSACTION;

    DELETE FROM MentorTecnico WHERE mentor_id = p_id;
    DELETE FROM Participante WHERE id = p_id;

    COMMIT;
END$$

DELIMITER ;

-- Estudiantes
INSERT INTO Participante (nombre, email, nivel_habilidad, tipo)
VALUES 
('Juan Pérez', 'juan@example.com', 'Intermedio', 'Estudiante'),
('María Gómez', 'maria@example.com', 'Avanzado', 'Estudiante'),
('Carlos López', 'carlos@example.com', 'Básico', 'Estudiante');

-- Insertar datos específicos de estudiante
INSERT INTO Estudiante (estudiante_id, grado, institucion, tiempo_disponible_semanal)
VALUES
(1, '3er Año', 'Colegio Nacional', 10),
(2, '4to Año', 'Instituto Central', 15),
(3, '2do Año', 'Colegio Regional', 8);

-- Mentores Técnicos
INSERT INTO Participante (nombre, email, nivel_habilidad, tipo)
VALUES
('Ana Torres', 'ana@example.com', 'Avanzado', 'Mentor'),
('Luis Fernández', 'luis@example.com', 'Intermedio', 'Mentor'),
('Carla Mendoza', 'carla@example.com', 'Experto', 'Mentor');

-- Insertar datos específicos de mentor técnico
INSERT INTO MentorTecnico (mentor_id, especialidad, experiencia, disponibilidad_horaria)
VALUES
(4, 'Inteligencia Artificial', 3, 15),
(5, 'Ciberseguridad', 5, 12),
(6, 'Desarrollo Web', 7, 20);
