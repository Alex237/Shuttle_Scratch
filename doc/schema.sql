SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `shuttle` DEFAULT CHARACTER SET utf8 ;
USE `shuttle` ;

-- -----------------------------------------------------
-- Table `shuttle`.`company`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `shuttle`.`company` (
  `idCompany` INT(11) NOT NULL ,
  `Name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`idCompany`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `shuttle`.`user`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `shuttle`.`user` (
  `idUser` INT(11) NOT NULL AUTO_INCREMENT ,
  `password` VARCHAR(255) NULL DEFAULT NULL ,
  `email` VARCHAR(255) NULL DEFAULT NULL ,
  `lastname` VARCHAR(45) NULL DEFAULT NULL ,
  `firstname` VARCHAR(45) NULL DEFAULT NULL ,
  `roles` TEXT NULL DEFAULT NULL ,
  `registerDate` DATETIME NULL DEFAULT NULL ,
  `lastLoginDate` DATETIME NULL DEFAULT NULL ,
  `state` INT(11) NOT NULL DEFAULT 0 ,
  `token` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`idUser`) ,
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 9
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `shuttle`.`project`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `shuttle`.`project` (
  `idProject` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL DEFAULT NULL ,
  `createDate` DATETIME NULL DEFAULT NULL ,
  `deadline` DATETIME NULL DEFAULT NULL ,
  `content` TEXT NULL ,
  `createdBy` INT(11) NOT NULL ,
  PRIMARY KEY (`idProject`) ,
  INDEX `fk_project_user1_idx` (`createdBy` ASC) ,
  CONSTRAINT `fk_project_user1`
    FOREIGN KEY (`createdBy` )
    REFERENCES `shuttle`.`user` (`idUser` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `shuttle`.`role`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `shuttle`.`role` (
  `label` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`label`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `shuttle`.`ticketstatus`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `shuttle`.`ticketstatus` (
  `idStatus` INT(11) NOT NULL AUTO_INCREMENT ,
  `statusLabel` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`idStatus`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `shuttle`.`tickettype`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `shuttle`.`tickettype` (
  `idTicketType` INT(11) NOT NULL AUTO_INCREMENT ,
  `label` VARCHAR(45) NOT NULL ,
  `steps` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`idTicketType`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `shuttle`.`ticket`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `shuttle`.`ticket` (
  `idTicket` INT(11) NOT NULL AUTO_INCREMENT ,
  `type` INT(11) NULL DEFAULT NULL ,
  `step` INT(11) NULL DEFAULT NULL ,
  `openDate` DATETIME NULL DEFAULT NULL ,
  `updateDate` DATETIME NULL DEFAULT NULL ,
  `closeDate` DATETIME NULL DEFAULT NULL ,
  `percent` INT(11) NOT NULL DEFAULT '0' ,
  `openBy` INT(11) NOT NULL ,
  `assignedTo` INT(11) NULL DEFAULT NULL ,
  `deadline` DATETIME NULL DEFAULT NULL ,
  `estimatedTime` INT(11) NULL DEFAULT NULL ,
  `title` VARCHAR(255) NOT NULL ,
  `content` TEXT NULL DEFAULT NULL ,
  `project` INT(11) NULL DEFAULT NULL ,
  `status` INT(11) NOT NULL ,
  `level` INT(11) NOT NULL ,
  PRIMARY KEY (`idTicket`) ,
  INDEX `fk_Ticket_TicketType_idx` (`type` ASC) ,
  INDEX `fk_Ticket_User2_idx` (`assignedTo` ASC) ,
  INDEX `fk_Ticket_Project1_idx` (`project` ASC) ,
  INDEX `fk_Ticket_User1_idx` (`openBy` ASC) ,
  INDEX `fk_Ticket_ticketstatus1_idx` (`status` ASC) ,
  CONSTRAINT `fk_Ticket_Project1`
    FOREIGN KEY (`project` )
    REFERENCES `shuttle`.`project` (`idProject` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Ticket_ticketstatus1`
    FOREIGN KEY (`status` )
    REFERENCES `shuttle`.`ticketstatus` (`idStatus` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Ticket_TicketType`
    FOREIGN KEY (`type` )
    REFERENCES `shuttle`.`tickettype` (`idTicketType` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Ticket_User1`
    FOREIGN KEY (`openBy` )
    REFERENCES `shuttle`.`user` (`idUser` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Ticket_User2`
    FOREIGN KEY (`assignedTo` )
    REFERENCES `shuttle`.`user` (`idUser` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `shuttle`.`token`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `shuttle`.`token` (
  `idtoken` INT NOT NULL AUTO_INCREMENT ,
  `token` VARCHAR(255) NOT NULL ,
  `expirationDate` DATETIME NOT NULL ,
  `user` INT(11) NOT NULL ,
  PRIMARY KEY (`idtoken`) ,
  INDEX `fk_token_user1_idx` (`user` ASC) ,
  CONSTRAINT `fk_token_user1`
    FOREIGN KEY (`user` )
    REFERENCES `shuttle`.`user` (`idUser` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `shuttle`.`message`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `shuttle`.`message` (
  `idMessage` INT NOT NULL AUTO_INCREMENT ,
  `content` TEXT NULL ,
  `createdDate` DATETIME NOT NULL ,
  `createdBy` INT(11) NOT NULL ,
  `ticket` INT(11) NOT NULL ,
  `changes` TEXT NULL ,
  PRIMARY KEY (`idMessage`) ,
  INDEX `fk_message_user1_idx` (`createdBy` ASC) ,
  INDEX `fk_message_ticket1_idx` (`ticket` ASC) ,
  CONSTRAINT `fk_message_user1`
    FOREIGN KEY (`createdBy` )
    REFERENCES `shuttle`.`user` (`idUser` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_message_ticket1`
    FOREIGN KEY (`ticket` )
    REFERENCES `shuttle`.`ticket` (`idTicket` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

USE `shuttle` ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
