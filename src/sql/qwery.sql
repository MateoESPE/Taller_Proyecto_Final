-- Creacion de tablas
    CREATE TABLE author(
        id int AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        username VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        password VARCHAR(100) NOT NULL,
        orcid VARCHAR(20) NOT NULL,
        affiliation VARCHAR(50) NOT NULL
    );

    CREATE TABLE publication(
        id int AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100) NOT NULL,
        description VARCHAR(100) NOT NULL,
        publication_date DATE NOT NULL,
        author_id INT NOT NULL,
        type ENUM('book','article') NOT NULL,
        Foreign Key (author_id) REFERENCES author(id)
            ON DELETE CASCADE
    )

    CREATE TABLE book(
        publication_id int AUTO_INCREMENT PRIMARY KEY,
        isbn VARCHAR(20) NOT NULL,
        genre VARCHAR(20) NOT NULL,
        edition int NOT NULL,
        Foreign Key (publication_id) REFERENCES publication(id)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    );

CREATE TABLE article(
    publication_id int AUTO_INCREMENT PRIMARY KEY,
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

-- Insertar 3 publicaciones (asociadas a autores)
INSERT INTO publication (title, description, publication_date, author_id, type) VALUES
('Libro de Física', 'Un libro completo de física básica', '2024-01-15', 1, 'book'),
('Artículo sobre IA', 'Un artículo sobre inteligencia artificial', '2024-05-10', 2, 'article'),
('Libro de Química', 'Introducción a la química orgánica', '2023-11-20', 3, 'book');

-- Insertar 2 libros (por las publicaciones de tipo 'book')
INSERT INTO book (publication_id, isbn, genre, edition) VALUES
(1, '978-3-16-148410-0', 'Ciencia', 1),
(3, '978-1-23-456789-7', 'Química', 2);

-- Insertar 1 artículo (por la publicación de tipo 'article')
INSERT INTO article (publication_id, doi, abstract, keywords, indexation, magazine, area) VALUES
(2, '10.1234/abcde.2024', 'Resumen del artículo sobre IA.', 'IA, Machine Learning, Redes Neuronales', 'Scopus', 'Revista de Tecnología', 'Tecnología');

-- Funciones book
CALL sp_book_list();
CALL sp_find_book(5);
CALL sp_create_book(
    'Introducción a la Programación',
    'Un libro para principiantes en programación.',
    '2023-01-01',
    1,
    '978-3-16-148410-0',
    'Informática',
    1
);
CALL sp_update_book(
    4,
    'Corridos',
    'Un libro para principiantes en programación, actualizado.',
    '2023-01-15',
    1,
    '978-3-16-148410-0',
    'Informática',
    2
);
CALL sp_delete_book(3);

-- Funciones article
CALL sp_article_list();
CALL sp_find_article(2);
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

CALL sp_update_article(
    6,                                                
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

CALL sp_delete_article(8);

DELIMITER $$

-- Procedimiento para listar todos los libros
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

-- Procedimiento para listar todos los artículos
CREATE OR REPLACE PROCEDURE sp_article_list()
BEGIN
    SELECT 
        a.`doi`,
        a.`abstract`,
        a.`keywords`,
        a.`indexation`,
        a.`magazine`,
        a.`area`,
        a.`publication_id`,
        p.id,
        p.`title`,
        p.`description`,
        p.`publication_date`,
        p.`type`,
        p.author_id,
        au.id AS author_id,
        au.first_name,
        au.last_name,
        au.username,
        au.email,
        au.orcid,
        au.affiliation
    FROM article a
        JOIN publication p ON a.publication_id = p.id
        JOIN author au ON p.author_id = au.id
    ORDER BY p.publication_date DESC;
END$$

-- Procedimiento para encontrar un artículo por su ID de publicación
CREATE OR REPLACE PROCEDURE sp_find_article(IN p_id INT)
BEGIN
    SELECT 
        a.`doi`,
        a.`abstract`,
        a.`keywords`,
        a.`indexation`,
        a.`magazine`,
        a.`area`,
        a.`publication_id`,
        p.id,
        p.`title`,
        p.`publication_date`,
        p.`type`,
        p.author_id,
        au.id,
        au.first_name,
        au.last_name
    FROM article a
        JOIN publication p ON a.publication_id = p.id
        JOIN author au ON p.author_id = au.id
    WHERE a.publication_id = p_id
    ORDER BY p.publication_date DESC; 
END$$

-- Procedimiento para crear un nuevo artículo
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

-- Procedimiento para actualizar un artículo
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

DELIMITER ;
