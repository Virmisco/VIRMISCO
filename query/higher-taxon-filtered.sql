SELECT
	substr(`taxa` FROM 1 FOR `position` - 2) `above`,
	`match`,
	substr(`taxa` FROM `position` + length(`match`) + 1) `below`
FROM
	(
		SELECT
			locate(
				lower(concat(' ', ifnull(:monomial, ''))),
				lower(concat(' ', trim(replace(`higher_taxa`, 0xa, ' '))))
			) `position`,
			substring_index(
				substr(
					trim(replace(`higher_taxa`, 0xa, ' ')),
					locate(
						lower(concat(' ', ifnull(:monomial, ''))),
						lower(concat(' ', trim(replace(`higher_taxa`, 0xa, ' '))))
					)
				),
				' ',
				1
			) `match`,
			trim(replace(`higher_taxa`, 0xa, ' ')) `taxa`
		FROM `organism`
		WHERE `higher_taxa` IS NOT NULL
			AND (
			ifnull(:monomial, '') = ''
				OR concat(' ', `higher_taxa`) LIKE concat('% ', nullif(:monomial, ''), '%')
		)
		GROUP BY `taxa`
	) `temp`;
