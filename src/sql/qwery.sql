-- Creacion de la base de datos
CREATE DATABASE IF NOT EXISTS project_db
    DEFAULT CHARACTER SET = 'utf8mb4';

USE project_db;

-- Creacion de tablas
CREATE TABLE IF NOT EXISTS author(
    id int AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    orcid VARCHAR(20) NOT NULL,
    affiliation VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS publication(
    id int AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description VARCHAR(100) NOT NULL,
    publication_date DATE NOT NULL,
    author_id INT NOT NULL,
    type ENUM('book','article') NOT NULL,
    FOREIGN KEY (author_id) REFERENCES author(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS book(
    publication_id int PRIMARY KEY,
    isbn VARCHAR(20) NOT NULL,
    genre VARCHAR(20) NOT NULL,
    edition int NOT NULL,
    FOREIGN KEY (publication_id) REFERENCES publication(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS article(
    publication_id int PRIMARY KEY,
    doi VARCHAR(20) NOT NULL,
    abstract VARCHAR(300) NOT NULL,
    keywords VARCHAR(50) NOT NULL,
    indexation VARCHAR(20) NOT NULL,
    magazine VARCHAR(100) NOT NULL,
    area VARCHAR(100) NOT NULL,
    FOREIGN KEY (publication_id) REFERENCES publication(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- Insertar 3 autores
INSERT INTO author (first_name, last_name, username, email, password, orcid, affiliation) VALUES
('Juan', 'Pérez', 'jperez', 'juan.perez@email.com', 'pass123', '0000-0001-2345-6789', 'Universidad A'),
('Ana', 'Gómez', 'agomez', 'ana.gomez@email.com', 'pass456', '0000-0002-3456-7890', 'Instituto B'),
('Luis', 'Martínez', 'lmartinez', 'luis.martinez@email.com', 'pass789', '0000-0003-4567-8901', 'Centro C');

-- Insertar 5 publicaciones (2 libros y 3 articulos)
INSERT INTO publication (title, description, publication_date, author_id, type) VALUES
('Libro de Física', 'Un libro completo de física básica', '2024-01-15', 1, 'book'),
('Artículo sobre IA', 'Un artículo sobre inteligencia artificial', '2024-05-10', 2, 'article'),
('Libro de Química', 'Introducción a la química orgánica', '2023-11-20', 3, 'book'),
('Artículo sobre Biología', 'Avances en biología molecular', '2023-12-01', 1, 'article'),
('Artículo sobre Astronomía', 'Exploración espacial y telescopios', '2024-03-15', 3, 'article');


INSERT INTO book (publication_id, isbn, genre, edition) VALUES
(1, '978-3-16-148410-0', 'Ciencia', 1),
(3, '978-1-23-456789-7', 'Química', 2);

INSERT INTO article (publication_id, doi, abstract, keywords, indexation, magazine, area) VALUES
(2, '10.1234/abcde.2024', 'Resumen del artículo sobre IA.', 'IA, Machine Learning, Redes Neuronales', 'Scopus', 'Revista de Tecnología', 'Tecnología'),
(4, '10.5678/fghij.2023', 'Resumen sobre biología molecular.', 'Biología, Molecular', 'PubMed', 'Revista de Ciencias Biológicas', 'Biología'),
(5, '10.9012/klmno.2024', 'Resumen sobre astronomía y telescopios.', 'Astronomía, Telescopios', 'Web of Science', 'Revista Astronómica', 'Astronomía');

-- Procedimientos de book
-- Procedimiento para listar todos los libros
DELIMITER $$
CREATE OR REPLACE PROCEDURE sp_book_list()
BEGIN
    SELECT 
        b.`isbn`,
        b.`genre`,
        b.`edition`,
        b.`publication_id`,
        p.id,
        p.`title`,
        p.`description`,
        p.`publication_date`,
        p.`type`,
        p.author_id,
        a.id,
        a.first_name,
        a.last_name
    FROM book b
        JOIN publication p ON b.publication_id = p.id
        JOIN author a ON p.author_id = a.id
    ORDER BY p.publication_date DESC;
END$$

CALL sp_book_list();

-- Procedimiento para encontrar un libro por su ID de publicación
CREATE OR REPLACE PROCEDURE sp_find_book(IN p_id INT)
BEGIN
    SELECT 
        b.`isbn`,
        b.`genre`,
        b.`edition`,
        b.`publication_id`,
        p.id,
        p.`title`,
        p.`description`,
        p.`publication_date`,
        p.`type`,
        p.author_id,
        a.id,
        a.first_name,
        a.last_name
    FROM book b
        JOIN publication p ON b.publication_id = p.id
        JOIN author a ON p.author_id = a.id
    WHERE b.publication_id = p_id
    ORDER BY p.publication_date DESC; 
END$$

CALL sp_find_book(6);

-- Procedimiento para crear un nuevo libro
CREATE OR REPLACE PROCEDURE sp_create_book(
    IN p_title              VARCHAR(255),
    IN p_description        TEXT,
    IN p_publication_date   DATE,
    IN p_author_id          INT,
    IN p_isbn               VARCHAR(20),
    IN p_genre              VARCHAR(20),
    IN p_edition            INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
    START TRANSACTION;
    INSERT INTO publication (title, description, publication_date, author_id, type)
    VALUES (p_title, p_description, p_publication_date, p_author_id, 'book');
    
    SET @new_pub_id := LAST_INSERT_ID();
    
    INSERT INTO book (publication_id, isbn, genre, edition)
    VALUES (@new_pub_id, p_isbn, p_genre, p_edition);
    
    COMMIT;
    SELECT @new_pub_id AS pub_id;
END$$

CALL sp_create_book(
    'Introducción a la Programación',
    'Un libro para principiantes en programación.',
    '2023-01-01',
    1,
    '978-3-16-148410-0',
    'Informática',
    1
);

-- Procedimiento para actualizar un libro
CREATE OR REPLACE PROCEDURE sp_update_book(
    IN p_publication_id     INT,
    IN p_title              VARCHAR(255),
    IN p_description        TEXT,
    IN p_publication_date   DATE,
    IN p_author_id          INT,
    IN p_isbn               VARCHAR(20),
    IN p_genre              VARCHAR(20),
    IN p_edition            INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
    START TRANSACTION;
    UPDATE publication
    SET title = p_title,
        description = p_description,
        publication_date = p_publication_date,
        author_id = p_author_id
    WHERE id = p_publication_id;

    UPDATE book
    SET isbn = p_isbn,
        genre = p_genre,
        edition = p_edition
    WHERE publication_id = p_publication_id;
    COMMIT;
END$$

CALL sp_update_book(
    7,
    'Corridos',
    'Un libro para principiantes en programación, actualizado.',
    '2023-01-15',
    1,
    '978-3-16-148410-0',
    'Informática',
    2
);

-- Procedimiento para eliminar un libro
CREATE OR REPLACE PROCEDURE sp_delete_book(IN p_id INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
    START TRANSACTION;
    DELETE FROM book WHERE publication_id = p_id;
    DELETE FROM publication WHERE id = p_id;
    COMMIT;
    SELECT 1 AS ok;
END$$

CALL sp_delete_book(7);

-- Procedimiento de articulos
-- Procedimiento para listar todos los artículos
CREATE OR REPLACE PROCEDURE sp_article_list()
BEGIN
    SELECT 
        ar.`doi`,
        ar.`abstract`,
        ar.`keywords`,
        ar.`indexation`,
        ar.`magazine`,
        ar.`area`,
        ar.`publication_id`,
        p.id,
        p.`title`,
        p.`description`,
        p.`publication_date`,
        p.`type`,
        p.author_id,
        a.id,
        a.first_name,
        a.last_name
    FROM article ar
        JOIN publication p ON ar.publication_id = p.id
        JOIN author a ON p.author_id = a.id
    ORDER BY p.publication_date DESC;
END$$

CALL sp_article_list();

-- Procedimiento para encontrar un articulo por su ID de publicacion
CREATE OR REPLACE PROCEDURE sp_find_article(IN p_id INT)
BEGIN
    SELECT 
        ar.`doi`,
        ar.`abstract`,
        ar.`keywords`,
        ar.`indexation`,
        ar.`magazine`,
        ar.`area`,
        ar.`publication_id`,
        p.id,
        p.`title`,
        p.`publication_date`,
        p.`type`,
        p.author_id,
        a.id,
        a.first_name,
        a.last_name
    FROM article ar
        JOIN publication p ON ar.publication_id = p.id
        JOIN author a ON p.author_id = a.id
    WHERE ar.publication_id = p_id
    ORDER BY p.publication_date DESC; 
END$$

CALL sp_find_article(8);

-- Procedimiento para crear un nuevo articulo
CREATE OR REPLACE PROCEDURE sp_create_article(
    IN p_title              VARCHAR(255),
    IN p_description        TEXT,
    IN p_publication_date   DATE,
    IN p_author_id          INT,
    IN p_doi                VARCHAR(20),
    IN p_abstract           VARCHAR(300),
    IN p_keywords           VARCHAR(50),
    IN p_indexation         VARCHAR(20),
    IN p_magazine           VARCHAR(100),
    IN p_area               VARCHAR(100)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
    START TRANSACTION;

    INSERT INTO publication (title, description, publication_date, author_id, type)
    VALUES (p_title, p_description, p_publication_date, p_author_id, 'article');

    SET @new_pub_id := LAST_INSERT_ID();

    INSERT INTO article (publication_id, doi, abstract, keywords, indexation, magazine, area)
    VALUES (@new_pub_id, p_doi, p_abstract, p_keywords, p_indexation, p_magazine, p_area);

    COMMIT;
    SELECT @new_pub_id AS pub_id;
END$$

CALL sp_create_article(
    'Informatica',                  
    'Un análisis profundo sobre ciberataques',        
    '2025-07-31',                                     
    1,                                                
    '10.1000/xyz123',                                
    'Resumen del artículo de ciberseguridad...',      
    'ciberseguridad, malware, redes',                 
    'Scopus',                                         
    'Revista de Seguridad Informática',               
    'Tecnología de la Información'                    
);

-- Procedimiento para actualizar un articulo
CREATE OR REPLACE PROCEDURE sp_update_article(
    IN p_publication_id     INT,
    IN p_title              VARCHAR(255),
    IN p_description        TEXT,
    IN p_publication_date   DATE,
    IN p_author_id          INT,
    IN p_doi                VARCHAR(20),
    IN p_abstract           VARCHAR(300),
    IN p_keywords           VARCHAR(50),
    IN p_indexation         VARCHAR(20),
    IN p_magazine           VARCHAR(100),
    IN p_area               VARCHAR(100)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
    START TRANSACTION;

    UPDATE publication
    SET title = p_title,
        description = p_description,
        publication_date = p_publication_date,
        author_id = p_author_id
    WHERE id = p_publication_id;

    UPDATE article
    SET doi = p_doi,
        abstract = p_abstract,
        keywords = p_keywords,
        indexation = p_indexation,
        magazine = p_magazine,
        area = p_area
    WHERE publication_id = p_publication_id;

    COMMIT;
END$$
CALL sp_update_article(
    8,                                                
    'Artículo actualizado sobre Ciberseguridad',      
    'Versión revisada del artículo original',        
    '2025-08-01',                                     
    1,                                                
    '10.1000/xyz123-v2',                              
    'Resumen actualizado con nuevas amenazas',        
    'ciberseguridad, ransomware, hacking ético',      
    'Web of Science',                                 
    'Cyber Defense Journal',                          
    'Ciencias de la Computación'                      
);

-- Procedimiento para eliminar un artículo
CREATE OR REPLACE PROCEDURE sp_delete_article(IN p_id INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
    START TRANSACTION;

    DELETE FROM article WHERE publication_id = p_id;
    DELETE FROM publication WHERE id = p_id;

    COMMIT;
    SELECT 1 AS ok;
END$$

CALL sp_delete_article(8);

DELIMITER ;
