# id
# title
# fileRealPath
# presentationUri
# genus
# specificEpithet
# sex
# typeStatus
# numPlanes
# focusStart
# focusEnd
SELECT
	`p`.`id`,
	`p`.`title`,
	`p`.`file__real_path` `fileRealPath`,
	`p`.`presentation_uri` `presentationUri`,
	`sn`.`genus`,
	`sn`.`specific_epithet` specificEpithet,
	`o`.`sex`,
	`o`.`type_designation__type_status` `typeStatus`,
	count(DISTINCT `fpi`.`id`) `numPlanes`,
	min(`fpi`.`focus_position`) `focusStart`,
	max(`fpi`.`focus_position`) `focusEnd`
FROM `photomicrograph` `p`
	JOIN `organism` `o` ON `o`.`id` = `p`.`organism_id`
	JOIN `scientific_name` `sn` ON `sn`.`specimen_carrier_id` = `o`.`specimen_carrier_id`
		AND `sn`.`sequence_number` = `o`.`sequence_number`
	JOIN `focal_plane_image` `fpi` ON `fpi`.`photomicrograph_id` = `p`.`id`
WHERE `sn`.`is_valid` = TRUE
	AND `p`.`file__real_path` LIKE concat_ws('/', '%', :parentDirName, :dirName, '%')
GROUP BY `p`.`id`;
