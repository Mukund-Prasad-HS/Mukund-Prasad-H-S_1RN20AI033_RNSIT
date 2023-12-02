SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";



CREATE TABLE users (
    user_id INT PRIMARY KEY,
    full_name VARCHAR(255),
    email VARCHAR(255),
    password VARCHAR(255),
    age INT,
    dob DATE,
    contact VARCHAR(20),
    address VARCHAR(255)
);


INSERT INTO users (user_id, full_name, email, password, age, dob, contact, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?);