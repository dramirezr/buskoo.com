SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

--CREATE SCHEMA IF NOT EXISTS `ecuamaps` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
--USE `ecuamaps` ;

-- -----------------------------------------------------
-- Table `user`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `user` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `email` VARCHAR(145) NOT NULL COMMENT 'As username' ,
  `passwd` VARCHAR(145) NOT NULL ,
  `name` VARCHAR(245) NOT NULL ,
  `email2` VARCHAR(145) NULL ,
  `address` VARCHAR(145) NULL ,
  `phone` VARCHAR(45) NULL ,
  `cellphone` VARCHAR(45) NULL ,
  `sex` ENUM('M','F') NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `preference`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `preference` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(145) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `type` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `id_parent` INT NULL DEFAULT NULL ,
  `name` VARCHAR(145) NOT NULL ,
  `tag` VARCHAR(145) NOT NULL ,
  `icon` VARCHAR(145) NULL ,
  `color` VARCHAR(45) NULL DEFAULT '#FFFFFF' ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `parent_type`
    FOREIGN KEY (`id_parent` )
    REFERENCES `type` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `parent_type_idx` ON `type` (`id_parent` ASC) ;


-- -----------------------------------------------------
-- Table `user_preference`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `user_preference` (
  `user_id` INT NOT NULL ,
  `preference_id` INT NOT NULL ,
  PRIMARY KEY (`user_id`, `preference_id`) ,
  CONSTRAINT `fk_user_preference_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_preference_preference1`
    FOREIGN KEY (`preference_id` )
    REFERENCES `preference` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_user_preference_user_idx` ON `user_preference` (`user_id` ASC) ;

CREATE INDEX `fk_user_preference_preference1_idx` ON `user_preference` (`preference_id` ASC) ;


-- -----------------------------------------------------
-- Table `business`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `business` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `user_id` INT NOT NULL ,
  `name` VARCHAR(145) NOT NULL ,
  `score_avg` FLOAT NULL DEFAULT 0.0 COMMENT 'Average from reviews' ,
  `address` VARCHAR(45) NULL ,
  `coordinate` VARCHAR(145) NOT NULL COMMENT 'Format: Lat,Long' ,
  `phones` VARCHAR(145) NULL COMMENT 'Comma separated' ,
  `CEO_name` VARCHAR(145) NULL COMMENT 'President, owner, etc' ,
  `last_modification` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_business_user1`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_business_user1_idx` ON `business` (`user_id` ASC) ;


-- -----------------------------------------------------
-- Table `type_preference`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `type_preference` (
  `preference_id` INT NOT NULL ,
  `type_id` INT NOT NULL ,
  CONSTRAINT `fk_business_type_preference_preference1`
    FOREIGN KEY (`preference_id` )
    REFERENCES `preference` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_business_type_preference_businesss_type1`
    FOREIGN KEY (`type_id` )
    REFERENCES `type` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_business_type_preference_preference1_idx` ON `type_preference` (`preference_id` ASC) ;

CREATE INDEX `fk_business_type_preference_businesss_type1_idx` ON `type_preference` (`type_id` ASC) ;


-- -----------------------------------------------------
-- Table `business_types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `business_types` (
  `business_id` INT NOT NULL ,
  `type_id` INT NOT NULL ,
  CONSTRAINT `fk_business_types_business1`
    FOREIGN KEY (`business_id` )
    REFERENCES `business` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_business_types_type1`
    FOREIGN KEY (`type_id` )
    REFERENCES `type` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_business_types_business1_idx` ON `business_types` (`business_id` ASC) ;

CREATE INDEX `fk_business_types_type1_idx` ON `business_types` (`type_id` ASC) ;


-- -----------------------------------------------------
-- Table `reviews`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `reviews` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `author_id` INT NOT NULL ,
  `author_ip` VARCHAR(45) NOT NULL ,
  `author_agent` VARCHAR(145) NOT NULL ,
  `table` ENUM('business','products') NOT NULL COMMENT 'Name of the table from which the comment come' ,
  `id_table` INT NOT NULL COMMENT 'Id of the review owner, product or business' ,
  `content` TEXT NULL ,
  `score` FLOAT NULL COMMENT 'Number of stars' ,
  `date` DATETIME NOT NULL ,
  `approved` ENUM('yes','no') NOT NULL DEFAULT 'no' ,
  `state` VARCHAR(45) NOT NULL DEFAULT 'active' ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_reviews_user1`
    FOREIGN KEY (`author_id` )
    REFERENCES `user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Reviews and scores made by the users';

CREATE INDEX `fk_reviews_user1_idx` ON `reviews` (`author_id` ASC) ;


-- -----------------------------------------------------
-- Table `products`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `products` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `business_id` INT NOT NULL ,
  `name` VARCHAR(145) NOT NULL ,
  `price` FLOAT NULL DEFAULT 0.0 ,
  `description` TEXT NULL ,
  `barcode` VARCHAR(145) NULL ,
  `tags` TEXT NULL ,
  `score_avg` FLOAT NULL DEFAULT 0.0 ,
  `last_modification` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_products_business1`
    FOREIGN KEY (`business_id` )
    REFERENCES `business` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'The products that belongs to a business';

CREATE INDEX `fk_products_business1_idx` ON `products` (`business_id` ASC) ;


-- -----------------------------------------------------
-- Table `media`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `media` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `user_id` INT NOT NULL ,
  `type` ENUM('logo','pic','video') NOT NULL ,
  `table` ENUM('business','products') NOT NULL ,
  `id_table` INT NOT NULL ,
  `hash` VARCHAR(145) NOT NULL ,
  `file_name` VARCHAR(145) NOT NULL ,
  `description` VARCHAR(250) NULL ,
  `upload_date` DATETIME NOT NULL ,
  `last_modification` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_media_user1`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_media_user1_idx` ON `media` (`user_id` ASC) ;


-- -----------------------------------------------------
-- Table `ads`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ads` (
  `id` INT NOT NULL ,
  `title` VARCHAR(145) NOT NULL ,
  `content` TEXT NOT NULL COMMENT 'Should be HTML' ,
  `coordinates` TEXT NULL COMMENT 'If ads must be seen at defined places, semicolon separated pairs' ,
  `clicks` INT NOT NULL ,
  `contract_amount` FLOAT NOT NULL DEFAULT 0.0 COMMENT 'The amount of money the customer paid' ,
  `apparitions` INT NOT NULL COMMENT 'Amount of the current apparitions' ,
  `apparition_price` FLOAT NOT NULL ,
  `remainig_money` FLOAT NULL DEFAULT 0.0 COMMENT 'Remaining money, must decrease on each apparition' ,
  `url` VARCHAR(300) NOT NULL ,
  `start_date` DATETIME NOT NULL ,
  `hash` VARCHAR(145) NULL COMMENT 'If has related some media' ,
  `client` VARCHAR(145) NOT NULL ,
  `email` VARCHAR(145) NOT NULL ,
  `client_user` VARCHAR(145) NULL COMMENT 'name of the client person' ,
  `status` ENUM('active','inactive') NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ads_preferences`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ads_preferences` (
  `preference_id` INT NOT NULL ,
  `ads_id` INT NOT NULL ,
  PRIMARY KEY (`preference_id`, `ads_id`) ,
  CONSTRAINT `fk_ads_preferences_preference1`
    FOREIGN KEY (`preference_id` )
    REFERENCES `preference` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ads_preferences_ads1`
    FOREIGN KEY (`ads_id` )
    REFERENCES `ads` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Helpful to identify on which preferences should be exposed';

CREATE INDEX `fk_ads_preferences_preference1_idx` ON `ads_preferences` (`preference_id` ASC) ;

CREATE INDEX `fk_ads_preferences_ads1_idx` ON `ads_preferences` (`ads_id` ASC) ;


-- -----------------------------------------------------
-- Table `config`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `config` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `key` VARCHAR(145) NOT NULL ,
  `value` TEXT NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `usermeta`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `usermeta` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `user_id` INT NOT NULL ,
  `meta_key` VARCHAR(145) NULL ,
  `meta_value` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_usermeta_user1`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'User Metafields';

CREATE INDEX `fk_usermeta_user1_idx` ON `usermeta` (`user_id` ASC) ;


-- -----------------------------------------------------
-- Table `metafields`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `metafields` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `source` VARCHAR(45) NOT NULL ,
  `name` VARCHAR(145) NOT NULL ,
  `CI_helper` VARCHAR(45) NULL DEFAULT 'form_input' COMMENT 'Custom helpers created at application/helpers/custom_form.php' ,
  `CI_helper_params` TEXT NULL COMMENT 'Must PHP serialized array' ,
  `help` VARCHAR(255) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
COMMENT = 'Defines the metafields';

CREATE TABLE IF NOT EXISTS  `ci_sessions` (
	session_id varchar(40) DEFAULT '0' NOT NULL,
	ip_address varchar(45) DEFAULT '0' NOT NULL,
	user_agent varchar(120) NOT NULL,
	last_activity int(10) unsigned DEFAULT 0 NOT NULL,
	user_data text NOT NULL,
	PRIMARY KEY (session_id),
	KEY `last_activity_idx` (`last_activity`)
);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

ALTER TABLE `post` ADD `icon` VARCHAR( 145 ) NULL AFTER `visits` ;