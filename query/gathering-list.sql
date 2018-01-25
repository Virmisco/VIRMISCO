# id
# journalNumber
# country
# province
# region
# place
# person
# organization
# dateAfter
# dateBefore
# remarks
SELECT
	`g`.`id`,
	`g`.`journal_number` `journalNumber`,
	`g`.`location__country` `country`,
	`g`.`location__province` `province`,
	`g`.`location__region` `region`,
	`g`.`location__place` `place`,
	`g`.`agent__person` `person`,
	`g`.`agent__organization` `organization`,
	`g`.`sampling_date__after` `dateAfter`,
	`g`.`sampling_date__before` `dateBefore`,
	`g`.`remarks`
FROM `gathering` `g`
ORDER BY `dateAfter` DESC, `dateBefore` ASC;
