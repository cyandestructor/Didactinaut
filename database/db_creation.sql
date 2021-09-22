CREATE DATABASE IF NOT EXISTS didactinaut_dev;
USE didactinaut_dev;

CREATE TABLE IF NOT EXISTS Images (
    image_id INT NOT NULL AUTO_INCREMENT,
    image_content MEDIUMBLOB,
    image_content_type VARCHAR(50),
    PRIMARY KEY (image_id)
);

CREATE TABLE IF NOT EXISTS Users (
    user_id INT NOT NULL AUTO_INCREMENT,
    user_username VARCHAR(50) NOT NULL UNIQUE,
    user_name VARCHAR(50) NOT NULL,
    user_lastname VARCHAR(50) NOT NULL,
    user_description TEXT,
    user_role ENUM('Instructor', 'User') DEFAULT 'User',
    user_email VARCHAR(60) NOT NULL UNIQUE,
    user_password VARCHAR(255) NOT NULL,
    account_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    account_last_change DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    user_is_public BIT DEFAULT 1,
    user_image INT,
    PRIMARY KEY (user_id),
    FOREIGN KEY (user_image)
        REFERENCES Images (image_id),
    FULLTEXT ( user_username , user_name , user_lastname )
);

CREATE TABLE IF NOT EXISTS Products (
    product_id INT NOT NULL AUTO_INCREMENT,
    product_price DECIMAL(15 , 2 ) NOT NULL DEFAULT 0,
    PRIMARY KEY (product_id)
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
    PRIMARY KEY (course_id),
    FOREIGN KEY (product_id)
        REFERENCES Products (product_id),
    FOREIGN KEY (course_instructor)
        REFERENCES Users (user_id),
    FULLTEXT ( course_title )
);

CREATE TABLE IF NOT EXISTS Sections (
    section_id INT NOT NULL AUTO_INCREMENT,
    section_title VARCHAR(50) NOT NULL,
    course_id INT NOT NULL,
    product_id INT,
    PRIMARY KEY (section_id),
    FOREIGN KEY (product_id)
        REFERENCES Products (product_id)
        ON DELETE SET NULL,
    FOREIGN KEY (course_id)
        REFERENCES Courses (course_id)
);

CREATE TABLE IF NOT EXISTS Lessons (
    lesson_id INT NOT NULL AUTO_INCREMENT,
    lesson_title VARCHAR(50) NOT NULL,
    lesson_text MEDIUMTEXT,
    section_id INT NOT NULL,
    PRIMARY KEY (lesson_id),
    FOREIGN KEY (section_id)
        REFERENCES Sections (section_id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Resources (
    resource_id INT NOT NULL AUTO_INCREMENT,
    resource_content LONGBLOB,
    resource_content_type VARCHAR(255),
    lesson_id INT NOT NULL,
    PRIMARY KEY (resource_id),
    FOREIGN KEY (lesson_id)
        REFERENCES Lessons (lesson_id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Videos (
    video_id INT NOT NULL AUTO_INCREMENT,
    video_address VARCHAR(255),
    video_duration INT,
    lesson_id INT,
    PRIMARY KEY (video_id),
    FOREIGN KEY (lesson_id)
        REFERENCES Lessons (lesson_id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Chats (
    chat_id INT NOT NULL AUTO_INCREMENT,
    chat_subject VARCHAR(80),
    PRIMARY KEY (chat_id)
);

CREATE TABLE IF NOT EXISTS Messages (
    message_id INT NOT NULL AUTO_INCREMENT,
    message_body MEDIUMTEXT,
    message_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    sender_user_id INT,
    chat_id INT NOT NULL,
    PRIMARY KEY (message_id),
    FOREIGN KEY (sender_user_id)
        REFERENCES Users (user_id)
        ON DELETE SET NULL,
    FOREIGN KEY (chat_id)
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
    PRIMARY KEY (review_id),
    FOREIGN KEY (user_id)
        REFERENCES Users (user_id),
    FOREIGN KEY (course_id)
        REFERENCES Courses (course_id)
);

CREATE TABLE IF NOT EXISTS Certificates (
    certificate_id BINARY(16) NOT NULL,
    expedition_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    instructor_id INT,
    PRIMARY KEY (certificate_id),
    FOREIGN KEY (instructor_id)
        REFERENCES Users (user_id)
);

CREATE TABLE IF NOT EXISTS PaymentMethods (
    payment_method_id INT NOT NULL AUTO_INCREMENT,
    payment_method_name VARCHAR(30) NOT NULL,
    PRIMARY KEY (payment_method_id)
);

CREATE TABLE IF NOT EXISTS Orders (
    order_id INT NOT NULL AUTO_INCREMENT,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    orderer_user_id INT NOT NULL,
    seller_user_id INT NOT NULL,
    payment_method INT NOT NULL,
    PRIMARY KEY (order_id),
    FOREIGN KEY (orderer_user_id)
        REFERENCES Users (user_id),
    FOREIGN KEY (seller_user_id)
        REFERENCES Users (user_id),
    FOREIGN KEY (payment_method)
        REFERENCES PaymentMethods (payment_method_id)
);

CREATE TABLE IF NOT EXISTS Categories (
    category_id INT NOT NULL AUTO_INCREMENT,
    category_name VARCHAR(50) NOT NULL,
    category_description TEXT,
    PRIMARY KEY (category_id)
);

CREATE TABLE IF NOT EXISTS Users_Courses (
    user_course_id INT NOT NULL AUTO_INCREMENT,
    enroll_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_time_checked DATETIME,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    certificate_id BINARY(16),
    PRIMARY KEY (user_course_id),
    FOREIGN KEY (user_id)
        REFERENCES Users (user_id),
    FOREIGN KEY (course_id)
        REFERENCES Courses (course_id),
    FOREIGN KEY (certificate_id)
        REFERENCES Certificates (certificate_id)
);

CREATE TABLE IF NOT EXISTS Courses_Categories (
    course_category_id INT NOT NULL AUTO_INCREMENT,
    course_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (course_category_id),
    FOREIGN KEY (category_id)
        REFERENCES Categories (category_id),
    FOREIGN KEY (course_id)
        REFERENCES Courses (course_id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Orders_Products (
    order_product_id INT NOT NULL AUTO_INCREMENT,
    final_product_price DECIMAL(15 , 2 ) NOT NULL,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    PRIMARY KEY (order_product_id),
    FOREIGN KEY (order_id)
        REFERENCES Orders (order_id),
    FOREIGN KEY (product_id)
        REFERENCES Products (product_id)
);

CREATE TABLE IF NOT EXISTS Users_Chats (
    user_chat_id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    chat_id INT NOT NULL,
    PRIMARY KEY (user_chat_id),
    FOREIGN KEY (user_id)
        REFERENCES Users (user_id),
    FOREIGN KEY (chat_id)
        REFERENCES Chats (chat_id)
);

CREATE TABLE IF NOT EXISTS Users_Lessons (
    user_lesson_id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    lesson_id INT NOT NULL,
    PRIMARY KEY (user_lesson_id),
    FOREIGN KEY (user_id)
        REFERENCES Users (user_id),
    FOREIGN KEY (lesson_id)
        REFERENCES Lessons (lesson_id)
);