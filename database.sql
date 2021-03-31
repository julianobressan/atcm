CREATE SCHEMA IF NOT EXISTS `atcm` DEFAULT CHARACTER SET utf8;

CREATE TABLE `atcm`.`user` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `login` VARCHAR(45) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT NOW(),
  `updated_at` DATETIME NULL,
  `deleted_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `login_UNIQUE` (`login` ASC) VISIBLE);

  CREATE TABLE `atcm`.`aircraft` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,  
  `model` VARCHAR(45) NOT NULL,
  `size` ENUM('small', 'large') NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT now(),
  `updated_at` DATETIME NULL,
  `deleted_at` DATETIME NULL,
  PRIMARY KEY (`id`));

CREATE TABLE `atcm`.`flight` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `aircraft_id` INT UNSIGNED NOT NULL,
  `flight_type` ENUM('emergency', 'vip', 'passenger', 'cargo') NOT NULL,
  `flight_number` VARCHAR(10) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT NOW(),
  `updated_at` DATETIME NULL,
  `deleted_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_flight_aircraft_idx` (`aircraft_id` ASC) VISIBLE,
  CONSTRAINT `fk_flight_aircraft`
    FOREIGN KEY (`aircraft_id`)
    REFERENCES `atcm`.`aircraft` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE);

