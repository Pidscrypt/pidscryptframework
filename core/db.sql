

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+03:00";

DROP DATABASE IF EXISTS com_humaneafricamission;

CREATE DATABASE IF NOT EXISTS com_humaneafricamission;
--
-- Table structure for table `groups`
--

CREATE TABLE if not exists com_humaneafricamission.groups(
    Id int primary key AUTO_INCREMENT,
    alias varchar(50) not null UNIQUE,
    permissions text null
)ENGINE=InnoDB;


CREATE TABLE if not exists com_humaneafricamission.users (
 user_Id int(11) NOT NULL AUTO_INCREMENT,
 user_fname varchar(20) NOT NULL,
 user_lname varchar(20) NOT NULL,
 user_oname varchar(20) NULL,
 user_alias varchar(50) NOT NULL UNIQUE,
 user_password varchar(250) NOT NULL,
 contact varchar(13) DEFAULT NULL,
 user_email varchar(150) NULL,
 dob date DEFAULT NULL,
 gender enum('male','female') NOT NULL,
 user_pic_url varchar(300) null,
 salt varchar(50) DEFAULT NULL,
 user_role int NOT NULL,
 reg_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 update_date timestamp NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (user_Id),
 foreign key (user_role) REFERENCES groups(Id)
)ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pages(
    id int primary key AUTO_INCREMENT,
    label varchar(20) not null,
    title varchar(250) not null,
    slug varchar(50) not null,
    created_at timestamp not null,
    updated_at timestamp null
)ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS content(
    
)ENGINE=InnoDB;

COMMIT;