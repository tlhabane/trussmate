DROP DATABASE IF EXISTS `trussmate`;
DROP USER IF EXISTS 'trussingmate'@'localhost';
CREATE DATABASE IF NOT EXISTS `trussmate` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'trussingmate'@'%' IDENTIFIED WITH mysql_native_password BY 't3u$$*R00T3R';
# GRANT ALL PRIVILEGES ON `finassist`.* TO 'finuser'@'%';
#GRANT CREATE, DROP, ALTER, REFERENCES ON *.* TO 'finuser'@'%';
GRANT ALL PRIVILEGES ON *.* TO 'trussingmate'@'%';
FLUSH PRIVILEGES;

# SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY','')); # Temporary
# SET PERSIST sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY','')); # Permanent, merges changes to /etc/my.cnf
# Enable 'ONLY_FULL_GROUP_BY'
# SET GLOBAL sql_mode=(SELECT CONCAT(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
# SET PERSIST sql_mode=(SELECT CONCAT(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
