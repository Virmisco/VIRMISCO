# id
# sequenceNumber
# typeStatus
# genus
# subgenus
# specificEpithet
# infraspecificEpithet
# validGenus
# validSubgenus
# validSpecificEpithet
# validInfraspecificEpithet
# numberOfPhotomicrographs
# carrierNumber
# country
# province
# region
# place
# person
# organization
# dateAfter
# dateBefore
SELECT
	`o`.`id`,
	`o`.`sequence_number` `sequenceNumber`,
	`o`.`type_designation__type_status` `typeStatus`,
	`sn`.`genus`,
	`sn`.`subgenus`,
	`sn`.`specific_epithet` `specificEpithet`,
	`sn`.`infraspecific_epithet` `infraspecificEpithet`,
	`vn`.`genus` `validGenus`,
	`vn`.`subgenus` `validSubgenus`,
	`vn`.`specific_epithet` `validSpecificEpithet`,
	`vn`.`infraspecific_epithet` `validInfraspecificEpithet`,
	count(DISTINCT `p`.`id`) `numberOfPhotomicrographs`,
	`sc`.`carrier_number` `carrierNumber`,
	`g`.`location__country` `country`,
	`g`.`location__province` `province`,
	`g`.`location__region` `region`,
	`g`.`location__place` `place`,
	`g`.`agent__person` `person`,
	`g`.`agent__organization` `organization`,
	COALESCE(`g`.`sampling_date__after`, '') `dateAfter`,
	COALESCE(`g`.`sampling_date__before`, '') `dateBefore`
FROM `organism` `o`
	JOIN `specimen_carrier` `sc` ON `sc`.`id` = `o`.`specimen_carrier_id`
	JOIN `gathering` `g` ON `g`.`id` = `sc`.`gathering_id`
	LEFT JOIN `scientific_name` `sn`
		ON `sn`.`specimen_carrier_id` = `o`.`specimen_carrier_id`
		AND `sn`.`sequence_number` = `o`.`sequence_number`
		AND `sn`.`is_mentioned` = 'true'
	LEFT JOIN `scientific_name` `vn`
		ON `vn`.`specimen_carrier_id` = `o`.`specimen_carrier_id`
		AND `vn`.`sequence_number` = `o`.`sequence_number`
		AND `vn`.`is_valid` = 'true'
	LEFT JOIN `photomicrograph` `p` ON `p`.`organism_id` = `o`.`id`
GROUP BY `o`.`id`
ORDER BY `dateAfter` DESC, `dateBefore` ASC;
