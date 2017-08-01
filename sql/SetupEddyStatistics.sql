CREATE TABLE IF NOT EXISTS `EddyStatistics` (
	`Id` 				INT NOT NULL AUTO_INCREMENT,
	`Name`				VARCHAR(255) NOT NULL,
	`Type`				VARCHAR(50) NOT NULL,
	`Enqueued`			INT NOT NULL DEFAULT 0,
	`Dequeued`			INT NOT NULL DEFAULT 0,
	`ErrorsCount`		INT NOT NULL DEFAULT 0,
	`Processed`			INT NOT NULL DEFAULT 0,
	`TotalRuntime`		DOUBLE NOT NULL DEFAULT 0,
	`Granularity`		INT NOT NULL,
	`DataDate`			DATETIME NOT NULL,
	
	PRIMARY KEY (`Id`),

	INDEX `k_Name` (`Name`)
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `EddyStatisticsSettings` (
	`Id` 				INT NOT NULL AUTO_INCREMENT,
	`Param`				VARCHAR(50) NOT NULL,
	`Value`				VARCHAR(255) NOT NULL,
	
	PRIMARY KEY (`Id`),
	
	UNIQUE KEY `k_EddyStatisticsSettings_Param` (`Param`)
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;