<?php
	namespace sednasoft\virmisco\readlayer\entity;
	use sednasoft\virmisco\readlayer\valueobject\GatheringAgent;
	use sednasoft\virmisco\readlayer\valueobject\GatheringLocation;
	use sednasoft\virmisco\readlayer\valueobject\GatheringSamplingDate;
	/**
	 * Do not edit. This class was automatically generated by codegen/read-layer/generator.php on
	 * 2016-01-13T22:53:08+01:00
	 */
	class Gathering {
		/** @var string */
		public static $agentClass = "sednasoft\\virmisco\\readlayer\\valueobject\\GatheringAgent";
		/** @var string */
		public static $locationClass = "sednasoft\\virmisco\\readlayer\\valueobject\\GatheringLocation";
		/** @var string */
		public static $samplingDateClass = "sednasoft\\virmisco\\readlayer\\valueobject\\GatheringSamplingDate";
		/** @var null|string */
		protected $agent__organization = null;
		/** @var null|string */
		protected $agent__person = null;
		/** @var string */
		protected $id = "";
		/** @var string */
		protected $journal_number = "";
		/** @var null|string */
		protected $location__country = null;
		/** @var null|string */
		protected $location__place = null;
		/** @var null|string */
		protected $location__province = null;
		/** @var null|string */
		protected $location__region = null;
		/** @var null|string */
		protected $remarks = null;
		/** @var null|string */
		protected $sampling_date__after = null;
		/** @var null|string */
		protected $sampling_date__before = null;

		/**
		 * @return GatheringAgent|null
		 */
		public function getAgent () {
			$className = self::$agentClass;
			return $this->agent__person !== null || $this->agent__organization !== null
				? new $className($this->agent__person, $this->agent__organization)
				: null;
		}

		/**
		 * @return string
		 */
		public function getId () {
			return $this->id;
		}

		/**
		 * @return string
		 */
		public function getJournalNumber () {
			return $this->journal_number;
		}

		/**
		 * @return GatheringLocation|null
		 */
		public function getLocation () {
			$className = self::$locationClass;
			return $this->location__country !== null || $this->location__province !== null || $this->location__region !== null || $this->location__place !== null
				? new $className($this->location__country, $this->location__province, $this->location__region, $this->location__place)
				: null;
		}

		/**
		 * @return null|string
		 */
		public function getRemarks () {
			return $this->remarks;
		}

		/**
		 * @return GatheringSamplingDate|null
		 */
		public function getSamplingDate () {
			$className = self::$samplingDateClass;
			return $this->sampling_date__after !== null || $this->sampling_date__before !== null
				? new $className($this->sampling_date__after, $this->sampling_date__before)
				: null;
		}
	}