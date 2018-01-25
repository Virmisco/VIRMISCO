<?php
    namespace sednasoft\virmisco\domain\projection;

    use Exception;
    use Generator;
    use InvalidArgumentException;
    use PDO;
    use PDOException;
    use Predis\Client;
    use sednasoft\virmisco\singiere\IProjection;

    /**
     * Maintains a Redis list of SQL statements yet to be sent to the report database to update the current state.
     */
    abstract class AbstractRedisBasedSqlQueueProjection implements IProjection
    {
        // tables and columns as of 2015-09-09
        const COL_CS_CREATION_TIME = 'creation_time';
        const COL_CS_ID = 'id';
        const COL_CS_MODIFICATION_TIME = 'modification_time';
        const COL_CS_REAL_PATH = 'real_path';
        const COL_CS_SPECIMEN_CARRIER_ID = 'specimen_carrier_id';
        const COL_CS_URI = 'uri';
        const COL_FPI_EXPOSURE_SETTINGS_DURATION = 'exposure_settings__duration';
        const COL_FPI_EXPOSURE_SETTINGS_GAIN = 'exposure_settings__gain';
        const COL_FPI_FILE_CREATION_TIME = 'file__creation_time';
        const COL_FPI_FILE_MODIFICATION_TIME = 'file__modification_time';
        const COL_FPI_FILE_REAL_PATH = 'file__real_path';
        const COL_FPI_FILE_URI = 'file__uri';
        const COL_FPI_FOCUS_POSITION = 'focus_position';
        const COL_FPI_HISTOGRAM_SETTINGS_BLACK_CLIP = 'histogram_settings__black_clip';
        const COL_FPI_HISTOGRAM_SETTINGS_GAMMA = 'histogram_settings__gamma';
        const COL_FPI_HISTOGRAM_SETTINGS_WHITE_CLIP = 'histogram_settings__white_clip';
        const COL_FPI_ID = 'id';
        const COL_FPI_PHOTOMICROGRAPH_ID = 'photomicrograph_id';
        const COL_FPI_POST_PROCESSING_SETTINGS_SHADING = 'post_processing_settings__shading';
        const COL_FPI_POST_PROCESSING_SETTINGS_SHARPENING = 'post_processing_settings__sharpening';
        const COL_FPI_PRESENTATION_URI = 'presentation_uri';
        const COL_G_AGENT_ORGANIZATION = 'agent__organization';
        const COL_G_AGENT_PERSON = 'agent__person';
        const COL_G_ID = 'id';
        const COL_G_JOURNAL_NUMBER = 'journal_number';
        const COL_G_LOCATION_COUNTRY = 'location__country';
        const COL_G_LOCATION_PLACE = 'location__place';
        const COL_G_LOCATION_PROVINCE = 'location__province';
        const COL_G_LOCATION_REGION = 'location__region';
        const COL_G_REMARKS = 'remarks';
        const COL_G_SAMPLING_DATE_AFTER = 'sampling_date__after';
        const COL_G_SAMPLING_DATE_BEFORE = 'sampling_date__before';
        const COL_O_HIGHER_TAXA = 'higher_taxa';
        const COL_O_ID = 'id';
        const COL_O_IDENTIFICATION_IDENTIFIER = 'identification__identifier';
        const COL_O_IDENTIFICATION_QUALIFIER = 'identification__qualifier';
        const COL_O_PHASE_OR_STAGE = 'phase_or_stage';
        const COL_O_REMARKS = 'remarks';
        const COL_O_SEQUENCE_NUMBER = 'sequence_number';
        const COL_O_SEX = 'sex';
        const COL_O_SPECIMEN_CARRIER_ID = 'specimen_carrier_id';
        const COL_O_TYPE_DESIGNATION_TYPE_STATUS = 'type_designation__type_status';
        const COL_P_CAM_ACTIVE_PIXELS_HOR = 'camera__active_pixels_hor';
        const COL_P_CAM_ACTIVE_PIXELS_VER = 'camera__active_pixels_ver';
        const COL_P_CAM_ADC_RESOLUTION = 'camera__adc_resolution';
        const COL_P_CAM_CAMERA_ASN = 'camera__camera_article_or_serial_number';
        const COL_P_CAM_CAMERA_MAKER = 'camera__camera_maker';
        const COL_P_CAM_CAMERA_NAME = 'camera__camera_name';
        const COL_P_CAM_CAPTURE_FORMAT = 'camera__capture_format';
        const COL_P_CAM_CHIP_HEIGHT = 'camera__chip_height';
        const COL_P_CAM_CHIP_WIDTH = 'camera__chip_width';
        const COL_P_CAM_COLOR_FILTER_ARRAY = 'camera__color_filter_array';
        const COL_P_CAM_DYNAMIC_RANGE = 'camera__dynamic_range';
        const COL_P_CAM_OPTICAL_FORMAT = 'camera__optical_format';
        const COL_P_CAM_PIXEL_HEIGHT = 'camera__pixel_height';
        const COL_P_CAM_PIXEL_WIDTH = 'camera__pixel_width';
        const COL_P_CAM_PROTECTIVE_COLOR_FILTER = 'camera__protective_color_filter';
        const COL_P_CAM_READOUT_NOISE = 'camera__readout_noise';
        const COL_P_CAM_SENSOR_ASN = 'camera__sensor_article_or_serial_number';
        const COL_P_CAM_SENSOR_MAKER = 'camera__sensor_maker';
        const COL_P_CAM_SENSOR_NAME = 'camera__sensor_name';
        const COL_P_CAM_SNR_MAX = 'camera__snr_max';
        const COL_P_CREATOR_CAPTURING = 'creator_capturing';
        const COL_P_CREATOR_PROCESSING = 'creator_processing';
        const COL_P_DET_HOTSPOT_X = 'detail_of__hotspot__x';
        const COL_P_DET_HOTSPOT_Y = 'detail_of__hotspot__y';
        const COL_P_DET_PHOTOMICROGRAPH_ID = 'detail_of__photomicrograph_id';
        const COL_P_DIG_COLOR_DEPTH = 'digitization_data__color_depth';
        const COL_P_DIG_HEIGHT = 'digitization_data__height';
        const COL_P_DIG_REPRODUCTION_SCALE_HORIZONTAL = 'digitization_data__reproduction_scale_horizontal';
        const COL_P_DIG_REPRODUCTION_SCALE_VERTICAL = 'digitization_data__reproduction_scale_vertical';
        const COL_P_DIG_WIDTH = 'digitization_data__width';
        const COL_P_FILE_CREATION_TIME = 'file__creation_time';
        const COL_P_FILE_MODIFICATION_TIME = 'file__modification_time';
        const COL_P_FILE_REAL_PATH = 'file__real_path';
        const COL_P_FILE_URI = 'file__uri';
        const COL_P_ID = 'id';
        const COL_P_MIC_CAMERA_MOUNT_ADAPTER_ASN = 'microscope__camera_mount_adapter_article_or_serial_number';
        const COL_P_MIC_CAMERA_MOUNT_ADAPTER_MAGNIFICATION = 'microscope__camera_mount_adapter_magnification';
        const COL_P_MIC_CAMERA_MOUNT_ADAPTER_MAKER = 'microscope__camera_mount_adapter_maker';
        const COL_P_MIC_CAMERA_MOUNT_ADAPTER_NAME = 'microscope__camera_mount_adapter_name';
        const COL_P_MIC_CONDENSER_ASN = 'microscope__condenser_article_or_serial_number';
        const COL_P_MIC_CONDENSER_MAKER = 'microscope__condenser_maker';
        const COL_P_MIC_CONDENSER_NAME = 'microscope__condenser_name';
        const COL_P_MIC_CONDENSER_TURRET_PRISM_ASN = 'microscope__condenser_turret_prism_article_or_serial_number';
        const COL_P_MIC_CONDENSER_TURRET_PRISM_MAKER = 'microscope__condenser_turret_prism_maker';
        const COL_P_MIC_CONDENSER_TURRET_PRISM_NAME = 'microscope__condenser_turret_prism_name';
        const COL_P_MIC_DIC_TURRET_PRISM_ASN = 'microscope__dic_turret_prism_article_or_serial_number';
        const COL_P_MIC_DIC_TURRET_PRISM_MAKER = 'microscope__dic_turret_prism_maker';
        const COL_P_MIC_DIC_TURRET_PRISM_NAME = 'microscope__dic_turret_prism_name';
        const COL_P_MIC_MAGNIFICATION_CHANGER_ASN = 'microscope__magnification_changer_article_or_serial_number';
        const COL_P_MIC_MAGNIFICATION_CHANGER_MAKER = 'microscope__magnification_changer_maker';
        const COL_P_MIC_MAGNIFICATION_CHANGER_NAME = 'microscope__magnification_changer_name';
        const COL_P_MIC_NOSEPIECE_OBJECTIVE_ASN = 'microscope__nosepiece_objective_article_or_serial_number';
        const COL_P_MIC_NOSEPIECE_OBJECTIVE_MAGNIFICATION = 'microscope__nosepiece_objective_magnification';
        const COL_P_MIC_NOSEPIECE_OBJECTIVE_MAKER = 'microscope__nosepiece_objective_maker';
        const COL_P_MIC_NOSEPIECE_OBJECTIVE_NAME = 'microscope__nosepiece_objective_name';
        const COL_P_MIC_NOSEPIECE_OBJECTIVE_NUMERICAL_APERTURE = 'microscope__nosepiece_objective_numerical_aperture';
        const COL_P_MIC_NOSEPIECE_OBJECTIVE_TYPE = 'microscope__nosepiece_objective_type';
        const COL_P_MIC_PORTS_ASN = 'microscope__ports_article_or_serial_number';
        const COL_P_MIC_PORTS_MAKER = 'microscope__ports_maker';
        const COL_P_MIC_PORTS_NAME = 'microscope__ports_name';
        const COL_P_MIC_STAND_ASN = 'microscope__stand_article_or_serial_number';
        const COL_P_MIC_STAND_MAKER = 'microscope__stand_maker';
        const COL_P_MIC_STAND_NAME = 'microscope__stand_name';
        const COL_P_ORGANISM_ID = 'organism_id';
        const COL_P_PRESENTATION_URI = 'presentation_uri';
        const COL_P_SET_APERTURE_DIAPHRAGM_OPENING = 'microscope_settings__aperture_diaphragm_opening';
        const COL_P_SET_CONTRAST_METHOD = 'microscope_settings__contrast_method';
        const COL_P_SET_DIC_PRISM_POSITION = 'microscope_settings__dic_prism_position';
        const COL_P_SET_FIELD_DIAPHRAGM_OPENING = 'microscope_settings__field_diaphragm_opening';
        const COL_P_SET_MAGCHANGER_MAGNIFICATION = 'microscope_settings__magnification_changer_magnification';
        const COL_P_TITLE = 'title';
        const COL_SC_CARRIER_NUMBER = 'carrier_number';
        const COL_SC_GATHERING_ID = 'gathering_id';
        const COL_SC_ID = 'id';
        const COL_SC_LABEL_TRANSCRIPT = 'label_transcript';
        const COL_SC_OWNER = 'owner';
        const COL_SC_PREPARATION_TYPE = 'preparation_type';
        const COL_SC_PREVIOUS_COLLECTION = 'previous_collection';
        const COL_SN_AUTHORSHIP = 'authorship';
        const COL_SN_GENUS = 'genus';
        const COL_SN_ID = 'id';
        const COL_SN_INFRASPECIFIC_EPITHET = 'infraspecific_epithet';
        const COL_SN_IS_MENTIONED = 'is_mentioned';
        const COL_SN_IS_PARENTHESIZED = 'is_parenthesized';
        const COL_SN_IS_VALID = 'is_valid';
        const COL_SN_SEQUENCE_NUMBER = 'sequence_number';
        const COL_SN_SPECIMEN_CARRIER_ID = 'specimen_carrier_id';
        const COL_SN_SPECIFIC_EPITHET = 'specific_epithet';
        const COL_SN_SUBGENUS = 'subgenus';
        const COL_SN_YEAR = 'year';
        const TBL_CARRIER_SCAN = 'carrier_scan';
        const TBL_FOCAL_PLANE_IMAGE = 'focal_plane_image';
        const TBL_GATHERING = 'gathering';
        const TBL_ORGANISM = 'organism';
        const TBL_PHOTOMICROGRAPH = 'photomicrograph';
        const TBL_SCIENTIFIC_NAME = 'scientific_name';
        const TBL_SPECIMEN_CARRIER = 'specimen_carrier';
        /** @var Client */
        private $client;
        private $queueName;

        /**
         * @param string $redisConnectionUri E. g. 'tcp://redis.example.com:6379'
         * @param string $queueName E. g. 'sql-queue'
         * @throws InvalidArgumentException When \Predis\Client::createOptions() or \Predis\Client::createConnection
         * throws it.
         */
        public function __construct($redisConnectionUri, $queueName)
        {
            $this->queueName = $queueName;
            $this->client = new Client($redisConnectionUri);
        }

        /**
         * Iterates through the queue and executes the statements on the given DB connection.
         *
         * @param PDO $databaseConnection
         * @return Generator Yields the statement just executed as a string.
         * @throws PDOException
         * @throws Exception
         */
        public function commitToDatabase(PDO $databaseConnection)
        {
            while ($value = $this->client->lpop($this->queueName)) {
                $command = json_decode($value);
                if (isset($command->class, $command->staticMethod)) {
                    $className = $command->class;
                    $methodName = $command->staticMethod;
                    try {
                        $result = $className::$methodName($databaseConnection);
                        yield sprintf('/* %s::%s */', $className, $methodName) => [$result];
                    } catch (Exception $e) {
                        // push failed statements back into the queue
                        $this->client->lpush($this->queueName, [$value]);
                        throw $e;
                    }
                } else {
                    $statement = $databaseConnection->prepare($command->statement);
                    try {
                        $statement->execute($command->values);
                        yield $command->statement => $command->values;
                    } catch (PDOException $e) {
                        // push failed statements back into the queue
                        $this->client->lpush($this->queueName, [$value]);
                        $statement = sprintf("-- ERROR: %s\n", $command->statement);
                        foreach ($command->values as $k => $v) {
                            $statement .= sprintf("\t-- %s: %s\n", $k, json_encode($v));
                        }
                        throw new PDOException($statement, 0, $e);
                    }
                }
            }
        }

        /**
         * Returns the \Predis\Client connected to the datastore holding the queue.
         *
         * @return Client
         */
        public function transform()
        {
            return $this->client;
        }

        /**
         * Queues a soft deletion.
         *
         * Columns can be specified by using one of the AbstractRedisBasedSqlQueueProjection::COL_* constants.
         *
         * @param string $tableName One of the AbstractRedisBasedSqlQueueProjection::TBL_* constants.
         * @param array $filters And-combined equality filters with column names as keys.
         */
        protected function queueDelete($tableName, array $filters)
        {
            $statement = sprintf(
                'DELETE FROM `%s` WHERE %s',
                $tableName,
                implode(
                    ' AND ',
                    array_map(
                        function ($key) {
                            return sprintf('ifnull(`%s`, \'\') = ifnull(?, \'\')', $key);
                        },
                        array_keys($filters)
                    )
                )
            );
            $this->client->rpush(
                $this->queueName,
                json_encode([
                    'statement' => $statement,
                    'values' => array_values($filters)
                ])
            );
        }

        /**
         * Queues an insert statement.
         *
         * Columns can be specified by using one of the AbstractRedisBasedSqlQueueProjection::COL_* constants.
         *
         * @param string $tableName One of the AbstractRedisBasedSqlQueueProjection::TBL_* constants.
         * @param array $definitions The values to use with column names as keys.
         */
        protected function queueInsert($tableName, array $definitions)
        {
            list($usec, $sec) = explode(' ', microtime());
            $statement = sprintf(
                'INSERT IGNORE INTO `%s` SET `_created` = %d.%06d, %s',
                $tableName,
                $sec,
                $usec * 1e6,
                implode(
                    ', ',
                    array_map(
                        function ($key) use ($definitions) {
                            $rhs = '?';
                            $value = $definitions[$key];
                            if ($value instanceof SqlQueueSubselect) {
                                $rhs = sprintf(
                                    '(select `%s` from `%s` where %s)',
                                    $value->column,
                                    $value->table,
                                    implode(
                                        ' AND ',
                                        array_map(
                                            function ($key) {
                                                return sprintf('ifnull(`%s`, \'\') = ifnull(?, \'\')', $key);
                                            },
                                            array_keys($value->filters)
                                        )
                                    )
                                );
                            }

                            return sprintf('`%s` = %s', $key, $rhs);
                        },
                        array_keys($definitions)
                    )
                )
            );
            $values = call_user_func_array(
                'array_merge',
                array_map(
                    function ($value) {
                        return ($value instanceof SqlQueueSubselect) ? array_values($value->filters) : [$value];
                    },
                    array_values($definitions)
                )
            );
            $this->client->rpush(
                $this->queueName,
                json_encode([
                    'statement' => $statement,
                    'values' => $values
                ])
            );
        }

        /**
         * Queues any single prepared statement and the given parameters.
         *
         * @param string $preparedStatementString
         * @param array $parameters
         */
        protected function queuePreparedStatement($preparedStatementString, array $parameters)
        {
            $this->client->rpush(
                $this->queueName,
                json_encode(['statement' => $preparedStatementString, 'values' => $parameters])
            );
        }

        /**
         * Queues a call to a static method of a class with the PDO object representing the database connection as the
         * first and only argument.
         *
         * @param string $className
         * @param string $methodName
         */
        protected function queueStaticMethod($className, $methodName)
        {
            $this->client->rpush(
                $this->queueName,
                json_encode(['class' => $className, 'staticMethod' => $methodName])
            );
        }

        /**
         * Queues an update statement.
         *
         * Subselects can be defined by using an object instead of a scalar within the $changes array, which has the
         * properties column, table and filters.
         *
         * Columns can be specified by using one of the AbstractRedisBasedSqlQueueProjection::COL_* constants.
         *
         * @param string $tableName One of the AbstractRedisBasedSqlQueueProjection::TBL_* constants.
         * @param array $changes The new values to use with column names as keys.
         * @param array $filters And-combined equality filters with column names as keys.
         */
        protected function queueUpdate($tableName, array $changes, array $filters)
        {
            list($usec, $sec) = explode(' ', microtime());
            $statement = sprintf(
                'UPDATE `%s` SET `_modified` = %d.%06d, %s WHERE %s',
                $tableName,
                $sec,
                $usec * 1e6,
                implode(
                    ', ',
                    array_map(
                        function ($key) use ($changes) {
                            $rhs = '?';
                            $value = $changes[$key];
                            if ($value instanceof SqlQueueSubselect) {
                                $rhs = sprintf(
                                    '(select `%s` from `%s` where %s)',
                                    $value->column,
                                    $value->table,
                                    implode(
                                        ' AND ',
                                        array_map(
                                            function ($key) {
                                                return sprintf('ifnull(`%s`, \'\') = ifnull(?, \'\')', $key);
                                            },
                                            array_keys($value->filters)
                                        )
                                    )
                                );
                            }

                            return sprintf('`%s` = %s', $key, $rhs);
                        },
                        array_keys($changes)
                    )
                ),
                implode(
                    ' AND ',
                    array_map(
                        function ($key) {
                            return sprintf('ifnull(`%s`, \'\') = ifnull(?, \'\')', $key);
                        },
                        array_keys($filters)
                    )
                )
            );
            $values = call_user_func_array(
                'array_merge',
                array_map(
                    function ($value) {
                        return ($value instanceof SqlQueueSubselect) ? array_values($value->filters) : [$value];
                    },
                    array_values($changes)
                )
            );
            $this->client->rpush(
                $this->queueName,
                json_encode([
                    'statement' => $statement,
                    'values' => array_merge($values, array_values($filters))
                ])
            );
        }
    }