# id
# gatheringId
# carrierNumber
# preparationType
# owner
# previousCollection
# labelTranscript
SELECT
	`sc`.`id`,
	`sc`.`gathering_id` `gatheringId`,
	`sc`.`carrier_number` `carrierNumber`,
	`sc`.`preparation_type` `preparationType`,
	`sc`.`owner`,
	`sc`.`previous_collection` `previousCollection`,
	`sc`.`label_transcript` `labelTranscript`
FROM `specimen_carrier` `sc`
WHERE `sc`.`id` = ?;
