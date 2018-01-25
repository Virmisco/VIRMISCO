# id
# specimenCarrierId
# sequenceNumber
# identifier
# qualifier
# typeStatus
# phaseOrStage
# sex
# remarks
# scientificName
# validName
# otherSynonyms
# higherTaxa
SELECT
	`o`.`id`,
	`o`.`specimen_carrier_id` `specimenCarrierId`,
	`o`.`sequence_number` `sequenceNumber`,
	`o`.`identification__identifier` `identifier`,
	`o`.`identification__qualifier` `qualifier`,
	`o`.`type_designation__type_status` `typeStatus`,
	`o`.`phase_or_stage` `phaseOrStage`,
	`o`.`sex`,
	`o`.`remarks`,
	concat_ws(' ',
		`sn`.`genus`,
		concat('(', nullif(`sn`.`subgenus`, ''), ')'),
		`sn`.`specific_epithet`,
		nullif(`sn`.`infraspecific_epithet`, ''),
		if(`sn`.`is_parenthesized`,
			concat('(', concat_ws(', ', `sn`.`authorship`, `sn`.`year`), ')'),
			concat_ws(', ', `sn`.`authorship`, `sn`.`year`)
		)
	) `scientificName`,
	concat_ws(' ',
		`vn`.`genus`,
		concat('(', nullif(`vn`.`subgenus`, ''), ')'),
		`vn`.`specific_epithet`,
		nullif(`vn`.`infraspecific_epithet`, ''),
		if(`vn`.`is_parenthesized`,
			concat('(', concat_ws(', ', `vn`.`authorship`, `vn`.`year`), ')'),
			concat_ws(', ', `vn`.`authorship`, `vn`.`year`)
		)
	) `validName`,
	group_concat(
		concat_ws(' ',
			`s`.`genus`,
			concat('(', nullif(`s`.`subgenus`, ''), ')'),
			`s`.`specific_epithet`,
			nullif(`s`.`infraspecific_epithet`, ''),
			if(`s`.`is_parenthesized`,
				concat('(', concat_ws(', ', `s`.`authorship`, `s`.`year`), ')'),
				concat_ws(', ', `s`.`authorship`, `s`.`year`)
			)
		)
		SEPARATOR 0xa
	) `otherSynonyms`,
	replace(trim(both ' ' from `o`.`higher_taxa`), ' ', 0xa) `higherTaxa`
FROM `organism` `o`
	LEFT JOIN `scientific_name` `sn`
		ON `sn`.`specimen_carrier_id` = `o`.`specimen_carrier_id`
		AND `sn`.`sequence_number` = `o`.`sequence_number`
		AND `sn`.`is_mentioned` = 'true'
	LEFT JOIN `scientific_name` `vn`
		ON `vn`.`specimen_carrier_id` = `o`.`specimen_carrier_id`
		AND `vn`.`sequence_number` = `o`.`sequence_number`
		AND `vn`.`is_valid` = 'true'
	LEFT JOIN `scientific_name` `s`
		ON `s`.`specimen_carrier_id` = `o`.`specimen_carrier_id`
		AND `s`.`sequence_number` = `o`.`sequence_number`
		AND `s`.`is_valid` = ''
		AND `s`.`is_mentioned` = ''
WHERE `o`.`id` = ?;