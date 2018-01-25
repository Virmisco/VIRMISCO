<?php
    namespace sednasoft\virmisco\vpmh;

    use DateTime;
    use DateTimeInterface;
    use DateTimeZone;
    use InvalidArgumentException;
    use OutOfBoundsException;
    use sednasoft\virmisco\oai\pmh\repository\Request;
    use sednasoft\virmisco\oai\pmh\repository\request\ListIdentifiers;
    use sednasoft\virmisco\oai\pmh\repository\request\ListRecords;
    use sednasoft\virmisco\oai\pmh\repository\request\ListSets;
    use sednasoft\virmisco\oai\pmh\repository\Response;
    use sednasoft\virmisco\opamih\AbstractHandler;

    /**
     *Handles requests that may carry a resumption token to resume a previous partial list response.
     */
    abstract class AbstractResumableRequestHandler extends AbstractHandler
    {

        /**
         * Decodes a resumption token into an array containing these members in order:
         *
         * - type:string the FQN of one out of these interfaces: {ListIdentifiers, ListRecords, ListSets}
         * - page:int the 0-based page number requested by the token
         * - pageSize:int the size of every page up to now and in future
         * - metadataPrefix:string|null the metadata prefix to restrict the result or null
         * - setSpec:string|null the set specifier to restrict the result or null
         * - from:DateTime the specified earliest date or -- if none was given -- the earliest possible date in the repo
         * - until:DateTime the specified latest date or -- if none was given -- the highest possible 32-bit date (year
         * 2106)
         *
         * @param string $token
         * @return array
         */
        protected function decodeToken($token)
        {
            // aaaaaaaa-bbbb-4ccc-deef-ffffgggggggg
            // a = from - earliest
            // b = page
            // c = page size
            // d = 10xx where xx = type
            // e = index of metadata prefix
            // f = index of set specifier
            // g = until - from
            $pcre = str_replace('#', '[0-9a-z]', '<^(#{8})-(#{4})-4(#{3})-([89ab])(#{2})(#)-(#{4})(#{8})$>i');
            if (preg_match($pcre, $token, $matches)) {
                list(, $fromDelta, $page, $pageSize, $type, $metadataIndex, $setHigh, $setLow, $untilDelta) = $matches;
                switch (hexdec($type) - 8) {
                    case 1:
                        $type = ListIdentifiers::class;
                        break;
                    case 2:
                        $type = ListRecords::class;
                        break;
                    case 3:
                        $type = ListSets::class;
                        break;
                    default:
                        throw new InvalidArgumentException('Malformed resumption token ' . $token);
                }
                $metadataIndex = hexdec($metadataIndex);
                $setIndex = hexdec($setHigh . $setLow);
                $metadataPrefix = $metadataIndex
                    ? strval($this->getMetadataFormatProvider()->getMetadataFormatAt($metadataIndex - 1))
                    : null;
                $setSpec = null;
                if ($setIndex) {
                    foreach ($this->getSetProvider()->iterateSetsInRange($setIndex - 1, 1) as $set) {
                        $setSpec = strval($set);
                    }
                }
                $earliestRecordTime = $this->getEarliestRecordTimeProvider()->getEarliestRecordTime();
                $timezone = new DateTimeZone('UTC');
                $from = sprintf('@%d', $earliestRecordTime->getTimestamp() + hexdec($fromDelta));
                $from = new DateTime($from, $timezone);
                $until = sprintf('@%d', $from->getTimestamp() + hexdec($untilDelta));
                $until = new DateTime($until, $timezone);

                return [$type, hexdec($page), hexdec($pageSize) ?: 4096, $metadataPrefix, $setSpec, $from, $until];
            } else {
                throw new InvalidArgumentException('Malformed resumption token ' . $token);
            }
        }

        /**
         * @param Request $request
         * @param int $page
         * @param int $pageSize
         * @param string|null $metadataPrefix
         * @param string|null $setSpec
         * @param DateTimeInterface|null $from
         * @param DateTimeInterface|null $until
         * @return string
         * @throws InvalidArgumentException
         * @throws OutOfBoundsException
         */
        protected function encodeToken(
            Request $request,
            $page,
            $pageSize,
            $metadataPrefix = null,
            $setSpec = null,
            DateTimeInterface $from = null,
            DateTimeInterface $until = null
        ) {
            // aaaaaaaa-bbbb-4ccc-deef-ffffgggggggg
            // a = from - earliest
            // b = page
            // c = page size
            // d = 10xx where xx = type
            // e = index of metadata prefix
            // f = index of set specifier
            // g = until - from
            $metadataIndex = 0;
            $setIndex = 0;
            $fromDelta = 0;
            $untilDelta = 0xffffffff;
            if ($request instanceof ListIdentifiers) {
                $type = 0x9;
            } elseif ($request instanceof ListRecords) {
                $type = 0xa;
            } elseif ($request instanceof ListSets) {
                $type = 0xb;
            } else {
                throw new InvalidArgumentException('Non-resumable type ' . get_class($request));
            }
            if ($pageSize === 4096) {
                $pageSize = 0;
            } elseif ($pageSize > 4096) {
                throw new OutOfBoundsException('Page size out of range ' . $metadataPrefix);
            }
            if ($metadataPrefix !== null) {
                $metadataIndex = $this->getMetadataFormatProvider()->indexOfMetadataFormat($metadataPrefix);
                if ($metadataIndex === null) {
                    throw new InvalidArgumentException('Invalid metadata prefix ' . $metadataPrefix);
                } elseif ($metadataIndex > 254) {
                    throw new OutOfBoundsException('Index of metadata prefix out of range ' . $metadataPrefix);
                }
                $metadataIndex++;
            }
            if ($setSpec !== null) {
                $setIndex = $this->getSetProvider()->indexOfSet($setSpec);
                if ($setIndex === null) {
                    throw new InvalidArgumentException('Invalid set specifier ' . $setSpec);
                } elseif ($setIndex > 1048574) {
                    throw new OutOfBoundsException('Index of set specifier out of range ' . $metadataPrefix);
                }
                $setIndex++;
            }
            if ($from) {
                $fromDelta = $from->getTimestamp()
                    - $this->getEarliestRecordTimeProvider()->getEarliestRecordTime()->getTimestamp();
            }
            if ($until) {
                $untilDelta = $until->getTimestamp() - $from->getTimestamp();
            }
            if ($fromDelta < 0) {
                throw new OutOfBoundsException('From out of range ' . $from->format(DateTime::ATOM));
            }
            if ($untilDelta < 0) {
                throw new OutOfBoundsException('Until out of range ' . $until->format(DateTime::ATOM));
            }

            return sprintf(
                '%08x-%04x-4%03x-%x%02x%x-%04x%08x',
                $fromDelta,
                $page,
                $pageSize,
                $type,
                $metadataIndex,
                $setIndex >> 16,
                $setIndex & 0xffff,
                $untilDelta
            );
        }

        /**
         * @return EarliestRecordTimeProvider
         */
        abstract protected function getEarliestRecordTimeProvider();

        /**
         * @return IMetadataFormatProvider
         */
        abstract protected function getMetadataFormatProvider();

        /**
         * @return ISetProvider
         */
        abstract protected function getSetProvider();
    }
