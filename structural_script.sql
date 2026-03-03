SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS points;
DROP TABLE IF EXISTS tags;
DROP TABLE IF EXISTS token;
DROP TABLE IF EXISTS offer;
DROP TABLE IF EXISTS subject;
DROP TABLE IF EXISTS tag;
DROP TABLE IF EXISTS message;
DROP TABLE IF EXISTS user_;
DROP TABLE IF EXISTS conversation;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE user_(
    email VARCHAR(70) PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    hashedpwd VARCHAR(100) NOT NULL DEFAULT 'not-verified',
    balance DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    role VARCHAR(15) NOT NULL DEFAULT 'not-verified',
    CONSTRAINT CHK_USER_EMAIL CHECK (email LIKE '%_@__%.__%'),
    CONSTRAINT CHK_USER_BAL CHECK (balance >= 0)
) DEFAULT CHARSET=utf8;

CREATE TABLE conversation(
    id_conv INT AUTO_INCREMENT,
    PRIMARY KEY(id_conv)
) DEFAULT CHARSET=utf8;

CREATE TABLE tag(
    tagname VARCHAR(50) PRIMARY KEY
) DEFAULT CHARSET=utf8;

CREATE TABLE token(
    token VARCHAR(32) PRIMARY KEY,
    email VARCHAR(70) NOT NULL,
    deadline TIMESTAMP DEFAULT (CURRENT_TIMESTAMP + INTERVAL 10 MINUTE) NOT NULL,
    FOREIGN KEY (email) REFERENCES user_(email) ON DELETE CASCADE
) DEFAULT CHARSET=utf8;

CREATE TABLE offer(
   ouid INT PRIMARY KEY AUTO_INCREMENT,
   owner VARCHAR(70) NOT NULL,
   title VARCHAR(50) NOT NULL,
   description TEXT,
   price DECIMAL(8,2) NOT NULL,
   creation_time DATETIME NOT NULL,
   deadline DATETIME NOT NULL,
   style ENUM('normal', 'amethyst', 'space', 'cat', 'bad-apple') default 'normal',
   quantity INT NOT NULL DEFAULT 1,
   type enum('offer', 'request') NOT NULL,
   FOREIGN KEY(owner) REFERENCES user_(email)
    ON DELETE CASCADE,
   CONSTRAINT CHK_OFFER_PRICE
    CHECK (price > 0),
   CONSTRAINT CHK_OFFER_TIME
    CHECK (creation_time < deadline)
) DEFAULT CHARSET=utf8;

CREATE TABLE subject(
   subject_name VARCHAR(50) PRIMARY KEY
) DEFAULT CHARSET=utf8;

CREATE TABLE message(
    id_msg INT AUTO_INCREMENT,
    content TEXT NOT NULL,
    date_msg DATETIME NOT NULL,
    id_conv INT NOT NULL,
    email VARCHAR(70) NOT NULL,
    PRIMARY KEY(id_msg),
    FOREIGN KEY(id_conv) REFERENCES conversation(id_conv) ON DELETE CASCADE,
    FOREIGN KEY(email) REFERENCES user_(email) ON DELETE CASCADE
) DEFAULT CHARSET=utf8;

CREATE TABLE tags(
   ouid INT,
   tagname VARCHAR(50),
   PRIMARY KEY(ouid, tagname),
   FOREIGN KEY(ouid) REFERENCES offer(ouid) 
    ON DELETE CASCADE,
   FOREIGN KEY(tagname) REFERENCES tag(tagname) 
    ON DELETE CASCADE
) DEFAULT CHARSET=utf8;

CREATE TABLE points(
   email VARCHAR(70),
   subject_name VARCHAR(50),
   points DECIMAL(5,2),
   PRIMARY KEY(email, subject_name),
   FOREIGN KEY(email) REFERENCES user_(email) 
    ON DELETE CASCADE,
   FOREIGN KEY(subject_name) REFERENCES subject(subject_name) 
    ON DELETE CASCADE,
   CONSTRAINT CHK_POINTS_PTS
    CHECK (points >= 0)
) DEFAULT CHARSET=utf8;

CREATE TABLE transactions(
    tid INT PRIMARY KEY AUTO_INCREMENT,
   email VARCHAR(70),
   ouid INT,
   amount DECIMAL(8,2) NOT NULL,
   transaction_time DATETIME NOT NULL,
    id_conv INT NOT NULL,
   FOREIGN KEY(email) REFERENCES user_(email) 
    ON DELETE CASCADE,
   FOREIGN KEY(ouid) REFERENCES offer(ouid) 
    ON DELETE CASCADE,
    FOREIGN KEY(id_conv)
     REFERENCES conversation(id_conv),
   CONSTRAINT CHK_TRANSACTION_AMOUNT
    CHECK (amount >= 0)
) DEFAULT CHARSET=utf8;

DELIMITER //

CREATE TRIGGER transaction_insert
BEFORE INSERT ON transactions
FOR EACH ROW
BEGIN
    IF NEW.id_conv IS NULL OR NEW.id_conv = 0 THEN
        INSERT INTO conversation () VALUES ();
        SET NEW.id_conv = LAST_INSERT_ID();
    END IF;
END; //

DELIMITER ;

SET NAMES utf8;
