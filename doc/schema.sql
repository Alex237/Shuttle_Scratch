SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP SCHEMA IF EXISTS `Shuttle` ;
CREATE SCHEMA IF NOT EXISTS `Shuttle` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `Shuttle` ;

-- -----------------------------------------------------
-- Table `Shuttle`.`User`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Shuttle`.`User` ;

CREATE  TABLE IF NOT EXISTS `Shuttle`.`User` (
  `idUser` INT NOT NULL AUTO_INCREMENT ,
  `password` VARCHAR(255) NULL ,
  `email` VARCHAR(255) NULL ,
  `lastname` VARCHAR(45) NULL ,
  `firstname` VARCHAR(45) NULL ,
  `roles` TEXT NULL ,
  `company` VARCHAR(255) NULL ,
  `registerDate` DATETIME NULL ,
  `lastLoginDate` DATETIME NULL ,
  `state` VARCHAR(45) NULL ,
  PRIMARY KEY (`idUser`) )
ENGINE = InnoDB;

CREATE UNIQUE INDEX `email_UNIQUE` ON `Shuttle`.`User` (`email` ASC) ;


-- -----------------------------------------------------
-- Table `Shuttle`.`TicketType`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Shuttle`.`TicketType` ;

CREATE  TABLE IF NOT EXISTS `Shuttle`.`TicketType` (
  `idTicketType` INT NOT NULL AUTO_INCREMENT ,
  `label` VARCHAR(45) NULL ,
  `steps` TEXT NULL ,
  PRIMARY KEY (`idTicketType`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Shuttle`.`Ticket`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Shuttle`.`Ticket` ;

CREATE  TABLE IF NOT EXISTS `Shuttle`.`Ticket` (
  `idTicket` INT NOT NULL AUTO_INCREMENT ,
  `step` INT NULL ,
  `openDate` DATETIME NULL ,
  `updateDate` DATETIME NULL ,
  `closeDate` DATETIME NULL ,
  `percent` INT NOT NULL DEFAULT 0 ,
  `type` INT NOT NULL ,
  `openBy` INT NOT NULL ,
  `assignedTo` INT NOT NULL ,
  `deadline` DATETIME NULL ,
  `estimatedTime` INT NULL ,
  `title` VARCHAR(255) NOT NULL ,
  `content` TEXT NULL ,
  PRIMARY KEY (`idTicket`) ,
  CONSTRAINT `fk_Ticket_TicketType`
    FOREIGN KEY (`type` )
    REFERENCES `Shuttle`.`TicketType` (`idTicketType` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Ticket_User1`
    FOREIGN KEY (`openBy` )
    REFERENCES `Shuttle`.`User` (`idUser` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Ticket_User2`
    FOREIGN KEY (`assignedTo` )
    REFERENCES `Shuttle`.`User` (`idUser` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Ticket_TicketType_idx` ON `Shuttle`.`Ticket` (`type` ASC) ;

CREATE INDEX `fk_Ticket_User1_idx` ON `Shuttle`.`Ticket` (`openBy` ASC) ;

CREATE INDEX `fk_Ticket_User2_idx` ON `Shuttle`.`Ticket` (`assignedTo` ASC) ;


-- -----------------------------------------------------
-- Table `Shuttle`.`Project`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Shuttle`.`Project` ;

CREATE  TABLE IF NOT EXISTS `Shuttle`.`Project` (
  `idProject` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `company` VARCHAR(45) NULL ,
  `createDate` DATETIME NULL ,
  `deadline` DATETIME NULL ,
  PRIMARY KEY (`idProject`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Shuttle`.`Role`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Shuttle`.`Role` ;

CREATE  TABLE IF NOT EXISTS `Shuttle`.`Role` (
  `label` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`label`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Shuttle`.`Project_Ticket`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Shuttle`.`Project_Ticket` ;

CREATE  TABLE IF NOT EXISTS `Shuttle`.`Project_Ticket` (
  `project` INT NOT NULL ,
  `ticket` INT NOT NULL ,
  PRIMARY KEY (`project`, `ticket`) ,
  CONSTRAINT `fk_Project_has_Ticket_Project1`
    FOREIGN KEY (`project` )
    REFERENCES `Shuttle`.`Project` (`idProject` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Project_has_Ticket_Ticket1`
    FOREIGN KEY (`ticket` )
    REFERENCES `Shuttle`.`Ticket` (`idTicket` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Project_has_Ticket_Ticket1_idx` ON `Shuttle`.`Project_Ticket` (`ticket` ASC) ;

CREATE INDEX `fk_Project_has_Ticket_Project1_idx` ON `Shuttle`.`Project_Ticket` (`project` ASC) ;

USE `Shuttle` ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
