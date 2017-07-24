CREATE TABLE IF NOT EXISTS `EddyEvent` (
	`Id` 				CHAR(35) NOT NULL,
	`Created` 			DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
	`Modified` 			TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`Name`				VARCHAR(255) NOT NULL,
	`State`				VARCHAR(32) NOT NULL,
	`EventInterface` 	VARCHAR(1024),
	`ProxyClass`		VARCHAR(1024),
	`ConfigClass`		VARCHAR(1024),
	`HandlerInterface`	VARCHAR(1024) NOT NULL,
	`Config`			TEXT,
	
	PRIMARY KEY (`Id`),
	
	INDEX `k_Created` (`Created`),
	INDEX `k_Modified` (`Modified`),
	INDEX `k_Name` (`Name`),
	INDEX `k_EventInterface` (`EventInterface`),
	INDEX `k_HandlerInterface` (`HandlerInterface`)
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `EddyHandler` (
	`Id` 				CHAR(35) NOT NULL,
	`Created` 			DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
	`Modified` 			TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`State`				VARCHAR(32) NOT NULL,
	`ClassName`			VARCHAR(1024) NOT NULL,
	`ConfigClass`		VARCHAR(1024),
	`Config`			TEXT,
	
	PRIMARY KEY (`Id`),
	
	INDEX `k_Created` (`Created`),
	INDEX `k_Modified` (`Modified`),
	INDEX `k_Class` (`Class`)
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `EddyConnection` (
	`Id` 			CHAR(35) NOT NULL,
	`EddyEventId`	CHAR(35) NOT NULL,
	`EddyHandlerId`	CHAR(35) NOT NULL,
	
	PRIMARY KEY (`Id`),
	
	INDEX `k_EddyEventId` (`EddyEventId`),
	INDEX `k_EddyHandlerId` (`EddyHandlerId`),
	
	UNIQUE INDEX `k_EddyEventId_EddyHandlerId` (`EddyEventId`, `EddyHandlerId`),
	
	CONSTRAINT `fk_EddyConnection_EddyEventId` 
		FOREIGN KEY (`EddyEventId`)
		REFERENCES `EddyEvent` (`Id`)
		ON DELETE CASCADE 
		ON UPDATE CASCADE,
	
	CONSTRAINT `fk_EddyConnection_EddyHandlerId` 
		FOREIGN KEY (`EddyHandlerId`)
		REFERENCES `EddyHandler` (`Id`)
		ON DELETE CASCADE 
		ON UPDATE CASCADE
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;