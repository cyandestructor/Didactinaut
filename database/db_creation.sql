CREATE DATABASE IF NOT EXISTS didactinaut_dev;
USE didactinaut_dev;

CREATE TABLE IF NOT EXISTS Images (
    image_id INT NOT NULL AUTO_INCREMENT,
    image_content MEDIUMBLOB,
    image_content_type VARCHAR(50),
    CONSTRAINT PK_Images PRIMARY KEY (image_id)
);

CREATE TABLE IF NOT EXISTS Users (
    user_id INT NOT NULL AUTO_INCREMENT,
    user_username VARCHAR(50) NOT NULL UNIQUE,
    user_name VARCHAR(50) NOT NULL,
    user_lastname VARCHAR(50) NOT NULL,
    user_description TEXT,
    user_role ENUM('Instructor', 'User') DEFAULT 'User',
    user_gender VARCHAR(20),
    user_birthdate DATE,
    user_email VARCHAR(60) NOT NULL UNIQUE,
    user_password VARCHAR(255) NOT NULL,
    account_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    account_last_change DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    user_is_public BIT DEFAULT 1,
    user_image INT,
    CONSTRAINT PK_Users PRIMARY KEY (user_id),
    CONSTRAINT FK_Courses_User_Image FOREIGN KEY (user_image)
        REFERENCES Images (image_id),
    FULLTEXT ( user_username , user_name , user_lastname )
);

CREATE TABLE IF NOT EXISTS Products (
    product_id INT NOT NULL AUTO_INCREMENT,
    product_price DECIMAL(15 , 2 ) NOT NULL DEFAULT 0,
    CONSTRAINT PK_Products PRIMARY KEY (product_id)
);

CREATE TABLE IF NOT EXISTS Courses (
    course_id INT NOT NULL AUTO_INCREMENT,
    course_title VARCHAR(70) NOT NULL,
    course_description TEXT,
    publication_date DATETIME,
    last_update DATETIME,
    course_published BIT DEFAULT 0,
    course_image INT,
    product_id INT NOT NULL,
    course_instructor INT NOT NULL,
    CONSTRAINT PK_Courses PRIMARY KEY (course_id),
    CONSTRAINT FK_Courses_Course_Image FOREIGN KEY (course_image) REFERENCES Images (image_id),
    CONSTRAINT FK_Courses_Product_Id FOREIGN KEY (product_id)
        REFERENCES Products (product_id),
    CONSTRAINT FK_Courses_Course_Instructor FOREIGN KEY (course_instructor)
        REFERENCES Users (user_id),
    FULLTEXT ( course_description )
);

CREATE TABLE IF NOT EXISTS Sections (
    section_id INT NOT NULL AUTO_INCREMENT,
    section_title VARCHAR(50) NOT NULL,
    course_id INT NOT NULL,
    product_id INT,
    CONSTRAINT PK_Sections PRIMARY KEY (section_id),
    CONSTRAINT FK_Sections_Product_Id FOREIGN KEY (product_id)
        REFERENCES Products (product_id)
        ON DELETE SET NULL,
    CONSTRAINT FK_Sections_Course_Id FOREIGN KEY (course_id)
        REFERENCES Courses (course_id)
);

CREATE TABLE IF NOT EXISTS Lessons (
    lesson_id INT NOT NULL AUTO_INCREMENT,
    lesson_title VARCHAR(50) NOT NULL,
    lesson_text MEDIUMTEXT,
    section_id INT NOT NULL,
    CONSTRAINT PK_Lessons PRIMARY KEY (lesson_id),
    CONSTRAINT FK_Lessons_Section_Id FOREIGN KEY (section_id)
        REFERENCES Sections (section_id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Resources (
    resource_id INT NOT NULL AUTO_INCREMENT,
    resource_name VARCHAR(60),
    resource_content LONGBLOB,
    resource_content_type VARCHAR(255),
    lesson_id INT NOT NULL,
    CONSTRAINT PK_Resources PRIMARY KEY (resource_id),
    CONSTRAINT FK_Resources_Lesson_Id FOREIGN KEY (lesson_id)
        REFERENCES Lessons (lesson_id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Videos (
    video_id INT NOT NULL AUTO_INCREMENT,
    video_address VARCHAR(255),
    video_duration INT,
    lesson_id INT,
    CONSTRAINT PK_Videos PRIMARY KEY (video_id),
    CONSTRAINT FK_Videos_Lesson_Id FOREIGN KEY (lesson_id)
        REFERENCES Lessons (lesson_id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Chats (
    chat_id INT NOT NULL AUTO_INCREMENT,
    chat_subject VARCHAR(80),
    CONSTRAINT PK_Chats PRIMARY KEY (chat_id)
);

CREATE TABLE IF NOT EXISTS Messages (
    message_id INT NOT NULL AUTO_INCREMENT,
    message_body MEDIUMTEXT,
    message_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    sender_user_id INT,
    chat_id INT NOT NULL,
    CONSTRAINT PK_Messages PRIMARY KEY (message_id),
    CONSTRAINT FK_Messages_Sender_User_Id FOREIGN KEY (sender_user_id)
        REFERENCES Users (user_id)
        ON DELETE SET NULL,
    CONSTRAINT FK_Messages_Chat_Id FOREIGN KEY (chat_id)
        REFERENCES Chats (chat_id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Reviews (
    review_id INT NOT NULL AUTO_INCREMENT,
    review_body TEXT,
    review_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    review_score INT,
    review_published BIT DEFAULT 1,
    user_id INT,
    course_id INT,
    CONSTRAINT PK_Reviews PRIMARY KEY (review_id),
    CONSTRAINT FK_Reviews_User_Id FOREIGN KEY (user_id)
        REFERENCES Users (user_id),
    CONSTRAINT FK_Reviews_Course_Id FOREIGN KEY (course_id)
        REFERENCES Courses (course_id)
);

CREATE TABLE IF NOT EXISTS Certificates (
    certificate_id BINARY(16) NOT NULL,
    expedition_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    instructor_id INT,
    CONSTRAINT PK_Certificates PRIMARY KEY (certificate_id),
    CONSTRAINT FK_Certificates_Instructor_Id FOREIGN KEY (instructor_id)
        REFERENCES Users (user_id)
);

CREATE TABLE IF NOT EXISTS PaymentMethods (
    payment_method_id INT NOT NULL AUTO_INCREMENT,
    payment_method_name VARCHAR(30) NOT NULL,
    CONSTRAINT PK_PaymentMethods PRIMARY KEY (payment_method_id)
);

CREATE TABLE IF NOT EXISTS Orders (
    order_id INT NOT NULL AUTO_INCREMENT,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    orderer_user_id INT NOT NULL,
    payment_method INT NOT NULL,
    CONSTRAINT PK_Orders PRIMARY KEY (order_id),
    CONSTRAINT FK_Orders_Orderer_User_Id FOREIGN KEY (orderer_user_id)
        REFERENCES Users (user_id),
    CONSTRAINT FK_Orders_Payment_Method FOREIGN KEY (payment_method)
        REFERENCES PaymentMethods (payment_method_id)
);

CREATE TABLE IF NOT EXISTS Categories (
    category_id INT NOT NULL AUTO_INCREMENT,
    category_name VARCHAR(50) NOT NULL,
    category_description TEXT,
    CONSTRAINT PK_Categories PRIMARY KEY (category_id)
);

CREATE TABLE IF NOT EXISTS Users_Courses (
    user_course_id INT NOT NULL AUTO_INCREMENT,
    enroll_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_time_checked DATETIME,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    certificate_id BINARY(16),
    CONSTRAINT PK_Users_Courses PRIMARY KEY (user_course_id),
    CONSTRAINT FK_Users_Courses_User_Id FOREIGN KEY (user_id)
        REFERENCES Users (user_id),
    CONSTRAINT FK_Users_Courses_Course_Id FOREIGN KEY (course_id)
        REFERENCES Courses (course_id),
    CONSTRAINT FK_Users_Courses_Certificate_Id FOREIGN KEY (certificate_id)
        REFERENCES Certificates (certificate_id)
);

CREATE TABLE IF NOT EXISTS Courses_Categories (
    course_category_id INT NOT NULL AUTO_INCREMENT,
    course_id INT NOT NULL,
    category_id INT NOT NULL,
    CONSTRAINT PK_Courses_Categories PRIMARY KEY (course_category_id),
    CONSTRAINT FK_Courses_Categories_Category_Id FOREIGN KEY (category_id)
        REFERENCES Categories (category_id),
    CONSTRAINT FK_Courses_Categories_Course_Id FOREIGN KEY (course_id)
        REFERENCES Courses (course_id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Orders_Products (
    order_product_id INT NOT NULL AUTO_INCREMENT,
    final_product_price DECIMAL(15 , 2 ) NOT NULL,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    CONSTRAINT PK_Orders_Products PRIMARY KEY (order_product_id),
    CONSTRAINT FK_Orders_Products_Order_Id FOREIGN KEY (order_id)
        REFERENCES Orders (order_id),
    CONSTRAINT FK_Orders_Products_Product_Id FOREIGN KEY (product_id)
        REFERENCES Products (product_id)
);

CREATE TABLE IF NOT EXISTS Users_Chats (
    user_chat_id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    chat_id INT NOT NULL,
    CONSTRAINT PK_Users_Chats_User_Chat_Id PRIMARY KEY (user_chat_id),
    CONSTRAINT FK_Users_Chats_User_Id FOREIGN KEY (user_id)
        REFERENCES Users (user_id),
    CONSTRAINT FK_Users_Chats_Chat_Id FOREIGN KEY (chat_id)
        REFERENCES Chats (chat_id)
);

CREATE TABLE IF NOT EXISTS Users_Lessons (
    user_lesson_id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    lesson_id INT NOT NULL,
    CONSTRAINT PK_Users_Lessons PRIMARY KEY (user_lesson_id),
    CONSTRAINT FK_Users_Lessons_User_Id FOREIGN KEY (user_id)
        REFERENCES Users (user_id),
    CONSTRAINT FK_Users_Lessons_Lesson_Id FOREIGN KEY (lesson_id)
        REFERENCES Lessons (lesson_id)
);

alter table courses add constraint FK_Courses_Course_Image foreign key (course_image) references Images (image_id);



-- DATA DICTIONARY---------------------------------------------------------------------------------------------------------------------------------

-- Images DD
ALTER TABLE Images MODIFY COLUMN image_id INT NOT NULL AUTO_INCREMENT comment 'Id de imagen';
ALTER TABLE Images MODIFY COLUMN image_content MEDIUMBLOB comment 'Contenido de la imagen';
ALTER TABLE Images MODIFY COLUMN image_content_type VARCHAR(50) comment 'Tipo de contenido de la imagen';

-- Users DD
ALTER TABLE Users MODIFY COLUMN user_id INT NOT NULL AUTO_INCREMENT comment 'Id de usuario';
ALTER TABLE Users MODIFY COLUMN user_username VARCHAR(50) NOT NULL UNIQUE comment 'Nombre de usuario';
ALTER TABLE Users MODIFY COLUMN user_name VARCHAR(50) NOT NULL comment 'Nombre o nombres del usuario';
ALTER TABLE Users MODIFY COLUMN user_lastname VARCHAR(50) NOT NULL comment 'Apellido o apellidos del usuario';
ALTER TABLE Users MODIFY COLUMN user_description TEXT comment 'Descripción del usuario';
ALTER TABLE Users MODIFY COLUMN user_role ENUM('Instructor', 'User') DEFAULT 'User' comment 'Rol de usuario';
ALTER TABLE Users MODIFY COLUMN user_gender VARCHAR(20) comment 'Género de usuario';
ALTER TABLE Users MODIFY COLUMN user_birthdate DATE comment 'Fecha de nacimiento de usuario';
ALTER TABLE Users MODIFY COLUMN user_email VARCHAR(60) NOT NULL UNIQUE comment 'Correo electrónico de usuario';
ALTER TABLE Users MODIFY COLUMN user_password VARCHAR(255) NOT NULL comment 'Contraseña de usuario';
ALTER TABLE Users MODIFY COLUMN account_creation DATETIME DEFAULT CURRENT_TIMESTAMP comment 'Fecha de creación de la cuenta de usuario';
ALTER TABLE Users MODIFY COLUMN account_last_change DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP comment 'Fecha de último cambio a la cuenta';
ALTER TABLE Users MODIFY COLUMN user_is_public BIT DEFAULT 1 comment 'Valida si el usuario es público o no';
ALTER TABLE Users MODIFY COLUMN user_image INT comment 'Imagen de usuario';

-- Products DD
ALTER TABLE Products MODIFY COLUMN product_id INT NOT NULL AUTO_INCREMENT comment 'Id de producto';
ALTER TABLE Products MODIFY COLUMN product_price DECIMAL(15 , 2 ) NOT NULL DEFAULT 0 comment 'Precio total del producto';

-- Courses DD
ALTER TABLE Courses MODIFY COLUMN course_id INT NOT NULL AUTO_INCREMENT comment 'Id de curso';
ALTER TABLE Courses MODIFY COLUMN course_title VARCHAR(70) NOT NULL comment 'Título de curso';
ALTER TABLE Courses MODIFY COLUMN course_description TEXT comment 'Descripción de curso';
ALTER TABLE Courses MODIFY COLUMN publication_date DATETIME comment 'Fecha de publicación de curso';
ALTER TABLE Courses MODIFY COLUMN last_update DATETIME comment 'Fecha de última actualización del curso';
ALTER TABLE Courses MODIFY COLUMN course_published BIT DEFAULT 0 comment 'Valida si el curso está publicado o no';
ALTER TABLE Courses MODIFY COLUMN course_image INT comment 'Imagen del curso';
ALTER TABLE Courses MODIFY COLUMN product_id INT NOT NULL comment 'Id del producto en el curso';
ALTER TABLE Courses MODIFY COLUMN course_instructor INT NOT NULL comment 'Id del instructor que imparte el curso';

-- Sections/Levels DD
ALTER TABLE Sections MODIFY COLUMN section_id INT NOT NULL AUTO_INCREMENT comment 'Id de sección o nivel';
ALTER TABLE Sections MODIFY COLUMN section_title VARCHAR(50) NOT NULL comment 'Título de sección o nivel';
ALTER TABLE Sections MODIFY COLUMN course_id INT NOT NULL comment 'Id del curso al que pertenece la sección';
ALTER TABLE Sections MODIFY COLUMN product_id INT comment 'Id del producto en la sección';

-- Lessons DD
ALTER TABLE Lessons MODIFY COLUMN lesson_id INT NOT NULL AUTO_INCREMENT comment 'Id de lección';
ALTER TABLE Lessons MODIFY COLUMN lesson_title VARCHAR(50) NOT NULL comment 'Título de lección';
ALTER TABLE Lessons MODIFY COLUMN lesson_text MEDIUMTEXT comment 'Texto en lección';
ALTER TABLE Lessons MODIFY COLUMN section_id INT NOT NULL comment 'Id de sección a la que pertenece la lección';

-- Resources DD
ALTER TABLE Resources MODIFY COLUMN resource_id INT NOT NULL AUTO_INCREMENT comment 'Id de recurso';
ALTER TABLE Resources MODIFY COLUMN resource_content LONGBLOB comment 'Contenido de recurso';
ALTER TABLE Resources MODIFY COLUMN resource_content_type VARCHAR(255) comment 'Tipo de contenido del recurso';
ALTER TABLE Resources MODIFY COLUMN lesson_id INT NOT NULL comment 'Id de lección a la que pertenece el recurso';

-- Videos DD
ALTER TABLE Videos MODIFY COLUMN video_id INT NOT NULL AUTO_INCREMENT comment 'Id de video';
ALTER TABLE Videos MODIFY COLUMN video_address VARCHAR(255) comment 'Dirección del video';
ALTER TABLE Videos MODIFY COLUMN video_duration INT comment 'Duración del video';
ALTER TABLE Videos MODIFY COLUMN lesson_id INT comment 'Id de lección a la que pertenece el video';

-- Chats DD
ALTER TABLE Chats MODIFY COLUMN chat_id INT NOT NULL AUTO_INCREMENT comment 'Id de chat';
ALTER TABLE Chats MODIFY COLUMN chat_subject VARCHAR(80) comment 'Sujeto del chat';

-- Messages DD
ALTER TABLE Messages MODIFY COLUMN message_id INT NOT NULL AUTO_INCREMENT comment 'Id de mensaje';
ALTER TABLE Messages MODIFY COLUMN message_body MEDIUMTEXT comment 'Cuerpo de mensaje';
ALTER TABLE Messages MODIFY COLUMN message_date DATETIME DEFAULT CURRENT_TIMESTAMP comment 'Fecha de envío de mensaje';
ALTER TABLE Messages MODIFY COLUMN sender_user_id INT comment 'Id de emisor de mensaje';
ALTER TABLE Messages MODIFY COLUMN chat_id INT NOT NULL comment 'Id de chat al que pertenecen los mensajes';

-- Reviews DD
ALTER TABLE Reviews MODIFY COLUMN review_id INT NOT NULL AUTO_INCREMENT comment 'Id de reseña';
ALTER TABLE Reviews MODIFY COLUMN review_body TEXT comment 'Cuerpo de reseña';
ALTER TABLE Reviews MODIFY COLUMN review_date DATETIME DEFAULT CURRENT_TIMESTAMP comment 'Fecha de publicación de la reseña';
ALTER TABLE Reviews MODIFY COLUMN review_score INT comment 'Puntuación dada en la reseña';
ALTER TABLE Reviews MODIFY COLUMN review_published BIT DEFAULT 1 comment 'Valida si la reseña está publicada o no';
ALTER TABLE Reviews MODIFY COLUMN user_id INT comment 'Id del usuario';
ALTER TABLE Reviews MODIFY COLUMN course_id INT comment 'Id del curso';

-- Certificates DD
ALTER TABLE Certificates MODIFY COLUMN certificate_id BINARY(16) NOT NULL comment 'Id de certificado';
ALTER TABLE Certificates MODIFY COLUMN expedition_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP comment 'Fecha de expedición de certificado';
ALTER TABLE Certificates MODIFY COLUMN instructor_id INT comment 'Id del instructor que imparte el certificado';

-- PaymentMethods DD
ALTER TABLE PaymentMethods MODIFY COLUMN payment_method_id INT NOT NULL AUTO_INCREMENT comment 'Id de método de pago';
ALTER TABLE PaymentMethods MODIFY COLUMN payment_method_name VARCHAR(30) NOT NULL comment 'Nombre del método de pago';

-- Orders DD
ALTER TABLE Orders MODIFY COLUMN order_id INT NOT NULL AUTO_INCREMENT comment 'Id de orden';
ALTER TABLE Orders MODIFY COLUMN order_date DATETIME DEFAULT CURRENT_TIMESTAMP comment 'Fecha de la orden';
ALTER TABLE Orders MODIFY COLUMN orderer_user_id INT NOT NULL comment 'Id del usuario que ordena';
ALTER TABLE Orders MODIFY COLUMN seller_user_id INT NOT NULL comment 'Id del usuario que vende';
ALTER TABLE Orders MODIFY COLUMN payment_method INT NOT NULL comment 'Id de método de pago en orden';

-- Categories DD
ALTER TABLE Categories MODIFY COLUMN category_id INT NOT NULL AUTO_INCREMENT comment 'Id de categoría';
ALTER TABLE Categories MODIFY COLUMN category_name VARCHAR(50) NOT NULL comment 'Nombre de categoría';
ALTER TABLE Categories MODIFY COLUMN category_description TEXT comment 'Decripción de categoría';

-- Users_Courses DD
ALTER TABLE Users_Courses MODIFY COLUMN user_course_id INT NOT NULL AUTO_INCREMENT comment 'Id de cursos adquiridos por usuarios';
ALTER TABLE Users_Courses MODIFY COLUMN enroll_date DATETIME DEFAULT CURRENT_TIMESTAMP comment 'Fecha de inscripción';
ALTER TABLE Users_Courses MODIFY COLUMN last_time_checked DATETIME comment 'Fecha en que fue revisado el curso por última vez';
ALTER TABLE Users_Courses MODIFY COLUMN user_id INT NOT NULL comment 'Id de usuario inscrito';
ALTER TABLE Users_Courses MODIFY COLUMN course_id INT NOT NULL comment 'Id de curso al que se ha inscrito';
ALTER TABLE Users_Courses MODIFY COLUMN certificate_id BINARY(16) comment 'Id del certificado de curso';

-- Courses_Categories DD
ALTER TABLE Courses_Categories MODIFY COLUMN course_category_id INT NOT NULL AUTO_INCREMENT comment 'Id de tabla categorías en cursos';
ALTER TABLE Courses_Categories MODIFY COLUMN course_id INT NOT NULL comment 'Id de curso al que pertenece la categoría';
ALTER TABLE Courses_Categories MODIFY COLUMN category_id INT NOT NULL comment 'Id de categoría en Courses_Categories';

-- Orders_Products DD
ALTER TABLE Orders_Products MODIFY COLUMN order_product_id INT NOT NULL AUTO_INCREMENT comment 'Id de tabla producto en orden';
ALTER TABLE Orders_Products MODIFY COLUMN final_product_price DECIMAL(15 , 2 ) NOT NULL comment 'Precio total del producto final';
ALTER TABLE Orders_Products MODIFY COLUMN order_id INT NOT NULL comment 'Id de orden en Orders_Products';
ALTER TABLE Orders_Products MODIFY COLUMN product_id INT NOT NULL comment 'Id de producto en Orders_Products';

-- Users_Chats DD
ALTER TABLE Users_Chats MODIFY COLUMN user_chat_id INT NOT NULL AUTO_INCREMENT comment 'Id de tabla chat de usuarios';
ALTER TABLE Users_Chats MODIFY COLUMN user_id INT NOT NULL comment 'Id de usuario en Users_Chats';
ALTER TABLE Users_Chats MODIFY COLUMN chat_id INT NOT NULL comment 'Id de chat en Users_Chats';

-- Users_Lessons DD
ALTER TABLE Users_Lessons MODIFY COLUMN  user_lesson_id INT NOT NULL AUTO_INCREMENT comment 'Id de lección de usuario';
ALTER TABLE Users_Lessons MODIFY COLUMN  user_id INT NOT NULL comment 'Id del usuario de la lección';
ALTER TABLE Users_Lessons MODIFY COLUMN  lesson_id INT NOT NULL comment 'Id de la lección de usuario';

SELECT distinct
		t.table_schema AS Nombre_BD,
        t.table_name AS Nombre_tabla,
        c.column_name AS Atributo,
        c.column_type AS Tipo_de_Dato,
        c.column_default AS Default_value,
        c.column_key AS Tipo_de_llave,
        c.is_nullable AS Es_Null,
        c.column_comment AS Descripcion
        
FROM information_schema.tables AS t
INNER JOIN information_schema.columns AS c
	ON t.table_name = c.table_name
	AND t.table_schema = c.table_schema
WHERE t.table_type IN ('BASE TABLE')
AND t.table_schema = 'didactinaut_dev'
ORDER BY
		c.column_name,
		c.ordinal_position;








