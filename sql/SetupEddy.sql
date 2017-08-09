CREATE TABLE IF NOT EXISTS `EddyEvent` (
	`Id` 				CHAR(35) NOT NULL,
	`Created` 			DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
	`Modified` 			TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`Name`				VARCHAR(255) NOT NULL,
	`State`				ENUM ('running', 'paused', 'stopped', 'deleted'),
	`EventInterface` 	TEXT NOT NULL,
	`ProxyClassName`	TEXT,
	`ConfigClassName`	TEXT,
	`HandlerInterface`	TEXT NOT NULL,
	`Delay`				FLOAT,
	`MaxBulkSize`		INT,
	`DelayBuffer`		FLOAT,
	`PackageSize`		INT,
	
	PRIMARY KEY (`Id`),
	
	UNIQUE KEY `k_EddyEvent_Name` (`Name`),
	
	INDEX `k_Created` (`Created`),
	INDEX `k_Modified` (`Modified`)
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `EddyHandler` (
	`Id` 				CHAR(35) NOT NULL,
	`Created` 			DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
	`Modified` 			TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`Name`				VARCHAR(255) NOT NULL,
	`State`				ENUM ('running', 'paused', 'stopped', 'deleted'),
	`HandlerClassName`	TEXT NOT NULL,
	`ConfigClassName`	TEXT,
	`Delay`				FLOAT,
	`MaxBulkSize`		INT,
	`DelayBuffer`		FLOAT,
	`PackageSize`		INT,
	
	PRIMARY KEY (`Id`),
	
	UNIQUE KEY `k_EddyHandler_Name` (`Name`),
	
	INDEX `k_Created` (`Created`),
	INDEX `k_Modified` (`Modified`)
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `EddySubscribers` (
	`Id` 			INT NOT NULL AUTO_INCREMENT,
	`Created` 		DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
	`EddyEventId`	CHAR(35) NOT NULL,
	`EddyHandlerId`	CHAR(35) NOT NULL,
	
	PRIMARY KEY (`Id`),
	
	INDEX `k_EddyEventId` (`EddyEventId`),
	INDEX `k_EddyHandlerId` (`EddyHandlerId`),
	INDEX `k_Created` (`Created`),
	
	UNIQUE INDEX `k_EddyEventId_EddyHandlerId` (`EddyEventId`, `EddyHandlerId`),
	
	CONSTRAINT `fk_EddySubscribers_EddyEventId` 
		FOREIGN KEY (`EddyEventId`)
		REFERENCES `EddyEvent` (`Id`)
		ON DELETE CASCADE 
		ON UPDATE CASCADE,
	
	CONSTRAINT `fk_EddySubscribers_EddyHandlerId` 
		FOREIGN KEY (`EddyHandlerId`)
		REFERENCES `EddyHandler` (`Id`)
		ON DELETE CASCADE 
		ON UPDATE CASCADE
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `EddyExecutors` (
	`Id` 			INT NOT NULL AUTO_INCREMENT,
	`Created` 		DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
	`EddyHandlerId`	CHAR(35) NOT NULL,
	`EddyEventId`	CHAR(35) NOT NULL,
	
	PRIMARY KEY (`Id`),
	
	INDEX `k_EddyHandlerId` (`EddyHandlerId`),
	INDEX `k_EddyEventId` (`EddyEventId`),
	INDEX `k_Created` (`Created`),
	
	UNIQUE INDEX `k_EddyHandlerId_EddyEventId` (`EddyHandlerId`, `EddyEventId`),

	CONSTRAINT `fk_EddyExecutors_EddyHandlerId`
	FOREIGN KEY (`EddyHandlerId`)
	REFERENCES `EddyHandler` (`Id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
	
	CONSTRAINT `fk_EddyExecutors_EddyEventId`
	FOREIGN KEY (`EddyEventId`)
	REFERENCES `EddyEvent` (`Id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `EddyEventSource` (
	`Id`			INT NOT NULL AUTO_INCREMENT,
	`Created`		DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
	`Source`		VARCHAR(255) NOT NULL,
	`EddyEventId`	CHAR(35) NOT NULL,
	`Status`		VARCHAR(32) NOT NULL,
	
	PRIMARY KEY (`Id`),
	
	INDEX `k_EddyEventId` (`EddyEventId`),
	INDEX `k_Created` (`Created`),
	
	UNIQUE INDEX `k_EddyEventId_Source` (`EddyEventId`, `Source`),

	CONSTRAINT `fk_EddyEventSource_EddyEventId`
	FOREIGN KEY (`EddyEventId`)
	REFERENCES `EddyEvent` (`Id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;