# id
# organismId
# validNameId
# higherTaxa
# genus
# subgenus
# specificEpithet
# infraspecificEpithet
# authorship
# year
# isParenthesized
# validGenus
# validSubgenus
# validSpecificEpithet
# validInfraspecificEpithet
# validAuthorship
# validYear
# validIsParenthesized
# isValid
SELECT
	`sn`.`id` `id`,
	`o`.`id` `organismId`,
	`vn`.`id` `validNameId`,
	`o`.`higher_taxa` `higherTaxa`,
	`sn`.`genus` `genus`,
	`sn`.`subgenus` `subgenus`,
	`sn`.`specific_epithet` `specificEpithet`,
	`sn`.`infraspecific_epithet` `infraspecificEpithet`,
	`sn`.`authorship` `authorship`,
	`sn`.`year` `year`,
	`sn`.`is_parenthesized` `isParenthesized`,
	`vn`.`genus` `validGenus`,
	`vn`.`subgenus` `validSubgenus`,
	`vn`.`specific_epithet` `validSpecificEpithet`,
	`vn`.`infraspecific_epithet` `validInfraspecificEpithet`,
	`vn`.`authorship` `validAuthorship`,
	`vn`.`year` `validYear`,
	`vn`.`is_parenthesized` `validIsParenthesized`,
	`vn`.`id` = `sn`.`id` `isValid`
FROM `scientific_name` `sn`
	LEFT JOIN `organism` `o`
		ON `o`.`specimen_carrier_id` = `sn`.`specimen_carrier_id`
		AND `o`.`sequence_number` = `sn`.`sequence_number`
	LEFT JOIN `scientific_name` `vn`
		ON `vn`.`specimen_carrier_id` = `o`.`specimen_carrier_id`
		AND `vn`.`sequence_number` = `o`.`sequence_number`
		AND `vn`.`is_valid` = 'true';
WHERE `sn`.`id` = ?;
