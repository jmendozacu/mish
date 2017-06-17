<?PHP
$this->startSetup();

$this->run("DROP TABLE IF EXISTS `eisbehr_qrmage_buffer`;
		   	CREATE TABLE `eisbehr_qrmage_buffer` (
				`hash` VARCHAR( 32 ) NOT NULL ,
				`time` INT( 10 ) NOT NULL ,
				`url` TEXT NOT NULL
			) ENGINE = MYISAM ;");

$this->endSetup();
