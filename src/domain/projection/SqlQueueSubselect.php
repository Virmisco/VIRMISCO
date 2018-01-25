<?php
    namespace sednasoft\virmisco\domain\projection;

    class SqlQueueSubselect
    {
        /** @var string */
        public $column = '';
        /** @var string[] */
        public $filters = [];
        /** @var string */
        public $table = '';

        /**
         * @param string $table
         * @param string $column
         */
        public function __construct($table, $column)
        {
            $this->column = $column;
            $this->table = $table;
        }

        /**
         * @param string $column
         * @param string|null $value
         */
        public function addFilter($column, $value)
        {
            $this->filters[$column] = $value;
        }
    }
