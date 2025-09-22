DROP TABLE IF EXISTS transaction;
DROP TABLE IF EXISTS points;
DROP TABLE IF EXISTS tags;
DROP TABLE IF EXISTS subject;
DROP TABLE IF EXISTS tag;
DROP TABLE IF EXISTS offer;
DROP TABLE IF EXISTS user_;

CREATE TABLE user_(
   email VARCHAR(70) PRIMARY KEY,
   username VARCHAR(50) NOT NULL,
   hashedpwd VARCHAR(100) NOT NULL,
   balance DECIMAL(8,2) NOT NULL DEFAULT 0.00,
   role VARCHAR(15) NOT NULL DEFAULT 'student',
   ADD CONSTRAINT CHK_USER_EMAIL
    CHECK (email LIKE '%_@__%.__%'),
   ADD CONSTRAINT CHK_USER_BAL
    CHECK (balance >= 0)
) DEFAULT CHARSET=utf8;

CREATE TABLE offer(
   ouid INT PRIMARY KEY,
   owner VARCHAR(70) NOT NULL,
   title VARCHAR(50) NOT NULL,
   description TEXT,
   price DECIMAL(8,2) NOT NULL,
   creation_time DATETIME NOT NULL,
   deadline DATETIME NOT NULL,
   FOREIGN KEY(owner) REFERENCES user_(email),
   ADD CONSTRAINT CHK_OFFER_PRICE
    CHECK (price >= 0),
   ADD CONSTRAINT CHK_OFFER_TIME
    CHECK (creation_time < deadline)
) DEFAULT CHARSET=utf8;

CREATE TABLE tag(
   tagname VARCHAR(50) PRIMARY KEY
) DEFAULT CHARSET=utf8;

CREATE TABLE subject(
   subject_name VARCHAR(50) PRIMARY KEY
) DEFAULT CHARSET=utf8;

CREATE TABLE tags(
   ouid INT,
   tagname VARCHAR(50),
   PRIMARY KEY(ouid, tagname),
   FOREIGN KEY(ouid) REFERENCES offer(ouid),
   FOREIGN KEY(tagname) REFERENCES tag(tagname)
) DEFAULT CHARSET=utf8;

CREATE TABLE points(
   email VARCHAR(70),
   subject_name VARCHAR(50),
   points DECIMAL(5,2),
   PRIMARY KEY(email, subject_name),
   FOREIGN KEY(email) REFERENCES user_(email),
   FOREIGN KEY(subject_name) REFERENCES subject(subject_name),
   ADD CONSTRAINT CHK_POINTS_PTS
    CHECK (points >= 0)
) DEFAULT CHARSET=utf8;

CREATE TABLE transaction(
   email VARCHAR(70),
   ouid INT,
   amount DECIMAL(8,2) NOT NULL,
   transaction_time DATETIME NOT NULL,
   PRIMARY KEY(email, ouid),
   FOREIGN KEY(email) REFERENCES user_(email),
   FOREIGN KEY(ouid) REFERENCES offer(ouid),
   ADD CONSTRAINT CHK_TRANSATION_AMOUNT
    CHECK (amount >= 0)
) DEFAULT CHARSET=utf8;

SET NAMES utf8;
