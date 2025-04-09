CREATE DATABASE IF NOT EXISTS library_db;
USE library_db;

CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    author VARCHAR(255),
    publisher VARCHAR(255),
    isbn VARCHAR(50),
    status ENUM('available','borrowed') DEFAULT 'available'
);

CREATE TABLE IF NOT EXISTS members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    status ENUM('active', 'inactive') DEFAULT 'active'
);

CREATE TABLE IF NOT EXISTS borrowings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT,
    member_id INT,
    borrow_date DATE,
    due_date DATE,
    return_date DATE,
    FOREIGN KEY (book_id) REFERENCES books(id),
    FOREIGN KEY (member_id) REFERENCES members(id)
);

INSERT INTO books (title, author, publisher, isbn) VALUES
('The Alchemist', 'Paulo Coelho', 'HarperOne', '9780061122415'),
('Clean Code', 'Robert C. Martin', 'Prentice Hall', '9780132350884');

INSERT INTO members (name, email) VALUES
('John Doe', 'john@example.com'),
('Jane Smith', 'jane@example.com');