-- Criação do banco de dados (caso ainda não exista)
CREATE DATABASE IF NOT EXISTS biblioteca;

-- Seleciona o banco de dados
USE biblioteca;

-- Criação da tabela de usuários
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Criação da tabela de livros
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inserção de alguns dados de exemplo na tabela de livros
INSERT INTO books (title, author) VALUES
('Dom Quixote', 'Miguel de Cervantes'),
('1984', 'George Orwell'),
('Cem Anos de Solidão', 'Gabriel García Márquez'),
('O Pequeno Príncipe', 'Antoine de Saint-Exupéry'),
('Crime e Castigo', 'Fiódor Dostoiévski');