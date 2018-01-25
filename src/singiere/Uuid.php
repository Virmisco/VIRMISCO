<?php
    namespace sednasoft\virmisco\singiere;

    use JsonSerializable;
    use sednasoft\virmisco\singiere\error\MalformedUuidException;

    /**
     * A universally unique identifier used for identifying aggregate roots.
     */
    final class Uuid implements JsonSerializable
    {
        private $hash;

        /**
         * Creates a new instance based on the given UUID.
         *
         * @param string $uuid A valid UUID in hyphen-separated hexadecimal groups.
         * @throws MalformedUuidException When the specified string does not follow the UUID syntax.
         */
        public function __construct($uuid)
        {
            if (!is_string($uuid)
                || strlen($uuid) !== 36
                || count($parts = explode('-', strtolower($uuid))) !== 5
                || strlen($parts[0]) !== 8
                || strlen($parts[1]) !== 4
                || strlen($parts[2]) !== 4
                || strlen($parts[3]) !== 4
                || strlen($parts[4]) !== 12
                || bin2hex(hex2bin($parts[0])) !== $parts[0]
                || bin2hex(hex2bin($parts[1])) !== $parts[1]
                || bin2hex(hex2bin($parts[2])) !== $parts[2]
                || bin2hex(hex2bin($parts[3])) !== $parts[3]
                || bin2hex(hex2bin($parts[4])) !== $parts[4]
            ) {
                throw new MalformedUuidException($uuid);
            }
            $this->hash = $uuid;
        }

        /**
         * Generates a new instance as a random version 4 UUID.
         *
         * @return Uuid A random UUID (version 4).
         */
        public static function createRandom()
        {
            return new self(
                sprintf(
                    '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                    mt_rand(0x0000, 0xffff),
                    mt_rand(0x0000, 0xffff),
                    mt_rand(0x0000, 0xffff),
                    mt_rand(0x4000, 0x4fff),
                    mt_rand(0x8000, 0xbfff),
                    mt_rand(0x0000, 0xffff),
                    mt_rand(0x0000, 0xffff),
                    mt_rand(0x0000, 0xffff)
                )
            );
        }

        /**
         * Returns the UUID as a string of hyphen-separated hexadecimal groups.
         *
         * @return string The UUID.
         */
        public function __toString()
        {
            return $this->hash;
        }

        /**
         * (PHP 5 &gt;= 5.4.0)<br/>
         * Specify data which should be serialized to JSON
         * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
         * @return mixed data which can be serialized by <b>json_encode</b>,
         * which is a value of any type other than a resource.
         */
        function jsonSerialize()
        {
            return 'urn:uuid:' . $this->hash;
        }
    }
