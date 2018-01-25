# id
# gatheringId
# carrierNumber
# preparationType
# owner
# previousCollection
# labelTranscript
# country
# province
# region
# place
# person
# organization
# dateAfter
# dateBefore
SELECT
	`sc`.`id`,
	`sc`.`gathering_id` `gatheringId`,
	`sc`.`carrier_number` `carrierNumber`,
	`sc`.`preparation_type` `preparationType`,
	`sc`.`owner`,
	`sc`.`previous_collection` `previousCollection`,
	`sc`.`label_transcript` `labelTranscript`,
	`g`.`location__country` `country`,
	`g`.`location__province` `province`,
	`g`.`location__region` `region`,
	`g`.`location__place` `place`,
	`g`.`agent__person` `person`,
	`g`.`agent__organization` `organization`,
	`g`.`sampling_date__after` `dateAfter`,
	`g`.`sampling_date__before` `dateBefore`
FROM `specimen_carrier` `sc`
	JOIN `gathering` `g` ON `g`.`id` = `sc`.`gathering_id`
ORDER BY `dateAfter` DESC, `dateBefore` ASC;
