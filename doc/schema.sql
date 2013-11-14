SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP SCHEMA IF EXISTS `Shuttle` ;
CREATE SCHEMA IF NOT EXISTS `Shuttle` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `Shuttle` ;

-- -----------------------------------------------------
-- Table `Shuttle`.`Company`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Shuttle`.`Company` ;

CREATE  TABLE IF NOT EXISTS `Shuttle`.`Company` (
  `idCompany` INT NOT NULL ,
  `Name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`idCompany`) )
ENGINE = InnoDB;


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
  `registerDate` DATETIME NULL ,
  `lastLoginDate` DATETIME NULL ,
  `state` VARCHAR(45) NULL ,
  `company` INT NULL ,
  PRIMARY KEY (`idUser`) ,
  CONSTRAINT `fk_User_Group1`
    FOREIGN KEY (`company` )
    REFERENCES `Shuttle`.`Company` (`idCompany` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE UNIQUE INDEX `email_UNIQUE` ON `Shuttle`.`User` (`email` ASC) ;

CREATE INDEX `fk_User_Group1_idx` ON `Shuttle`.`User` (`company` ASC) ;


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
-- Table `Shuttle`.`Project`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Shuttle`.`Project` ;

CREATE  TABLE IF NOT EXISTS `Shuttle`.`Project` (
  `idProject` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `createDate` DATETIME NULL ,
  `deadline` DATETIME NULL ,
  `company` INT NOT NULL ,
  PRIMARY KEY (`idProject`) ,
  CONSTRAINT `fk_Project_company1`
    FOREIGN KEY (`company` )
    REFERENCES `Shuttle`.`Company` (`idCompany` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Project_company1_idx` ON `Shuttle`.`Project` (`company` ASC) ;


-- -----------------------------------------------------
-- Table `Shuttle`.`Ticket`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Shuttle`.`Ticket` ;

CREATE  TABLE IF NOT EXISTS `Shuttle`.`Ticket` (
  `idTicket` INT NOT NULL AUTO_INCREMENT ,
  `type` INT NOT NULL ,
  `step` INT NULL ,
  `openDate` DATETIME NULL ,
  `updateDate` DATETIME NULL ,
  `closeDate` DATETIME NULL ,
  `percent` INT NOT NULL DEFAULT 0 ,
  `openBy` INT NOT NULL ,
  `assignedTo` INT NULL ,
  `deadline` DATETIME NULL ,
  `estimatedTime` INT NULL ,
  `title` VARCHAR(255) NOT NULL ,
  `content` TEXT NULL ,
  `project` INT NOT NULL ,
  PRIMARY KEY (`idTicket`) ,
  CONSTRAINT `fk_Ticket_TicketType`
    FOREIGN KEY (`type` )
    REFERENCES `Shuttle`.`TicketType` (`idTicketType` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Ticket_User2`
    FOREIGN KEY (`assignedTo` )
    REFERENCES `Shuttle`.`User` (`idUser` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Ticket_Project1`
    FOREIGN KEY (`project` )
    REFERENCES `Shuttle`.`Project` (`idProject` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Ticket_User1`
    FOREIGN KEY (`openBy` )
    REFERENCES `Shuttle`.`User` (`idUser` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Ticket_TicketType_idx` ON `Shuttle`.`Ticket` (`type` ASC) ;

CREATE INDEX `fk_Ticket_User2_idx` ON `Shuttle`.`Ticket` (`assignedTo` ASC) ;

CREATE INDEX `fk_Ticket_Project1_idx` ON `Shuttle`.`Ticket` (`project` ASC) ;

CREATE INDEX `fk_Ticket_User1_idx` ON `Shuttle`.`Ticket` (`openBy` ASC) ;


-- -----------------------------------------------------
-- Table `Shuttle`.`Role`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Shuttle`.`Role` ;

CREATE  TABLE IF NOT EXISTS `Shuttle`.`Role` (
  `label` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`label`) )
ENGINE = InnoDB;

USE `Shuttle` ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
