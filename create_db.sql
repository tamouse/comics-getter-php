/* create the database table -- should use only once -- do not store password in .git */
/* run as mysql root */

DROP DATABASE IF EXISTS comicsgetter;
CREATE DATABASE comicsgetter;

GRANT ALL ON comicsgetter.* TO 'cguser'@'localhost' IDENTIFIED BY 'rid7yis9an4yo9f';

SHOW DATABASES;
