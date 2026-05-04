CREATE DATABASE concern_track;
USE concern_track;

CREATE TABLE departments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50), -- Academic, Financial, Welfare
    email VARCHAR(100)
);

CREATE TABLE concerns (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ticket_id VARCHAR(10) UNIQUE,
    category VARCHAR(50),
    priority ENUM('Low', 'Medium', 'Urgent') DEFAULT 'Low',
    description TEXT,
    attachment VARCHAR(255),
    is_anonymous BOOLEAN DEFAULT 0,
    student_email VARCHAR(100),
    status ENUM('Submitted', 'Routed', 'Read', 'Screened', 'Resolved', 'Escalated') DEFAULT 'Submitted',
    dept_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    read_at DATETIME NULL,
    screened_at DATETIME NULL,
    FOREIGN KEY (dept_id) REFERENCES departments(id)
);

CREATE TABLE audit_trail (
    id INT PRIMARY KEY AUTO_INCREMENT,
    concern_id INT,
    action VARCHAR(255),
    actor VARCHAR(100),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255)
);

-- Ang password na ito ay 'admin123' (hashed)
INSERT INTO admins (username, password) VALUES 
('admin_dev', '$2y$10$898989898989898989898u.0.Y.Y.Y.Y.Y.Y.Y.Y.Y.Y.Y.Y.');

INSERT INTO departments (name, email) VALUES 
('Academic', 'academic.dept@school.edu'),
('Financial', 'finance.dept@school.edu'),
('Welfare', 'guidance.dept@school.edu');

CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_number VARCHAR(50) UNIQUE,
    full_name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    concern_id INT,
    action VARCHAR(255),
    actor VARCHAR(100),
    timestamp DATETIME
);