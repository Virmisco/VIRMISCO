<?php
    namespace sednasoft\virmisco\readlayer;

    use DateTime;
    use DateTimeZone;
    use sednasoft\virmisco\readlayer\valueobject\GatheringSamplingDate;

    class AugmentedSamplingDate extends GatheringSamplingDate
    {
        /**
         * @return string
         */
        public function __toString()
        {
            if($this->after == null && $this->before == null)
            	return "";
            
            if($this->after == null)
            	return substr($this->before, 0, 10);
            if($this->before == null)
            	return substr($this->after, 0, 10);
        
            // set to UTC noon, otherwise we get P30D days instead of P1M over DST shifts
            $after = (new DateTime($this->after))->setTimezone(new DateTimeZone('UTC'))->setTime(12, 0, 0);
            $before = (new DateTime($this->before))->setTimezone(new DateTimeZone('UTC'))->setTime(12, 0, 0);
            
            $before->modify('+1 day');
            
            // calculate difference as ISO 8601 duration
            $duration = $before->diff($after)->format('/P%yY%mM%dDT%hH%iM%sS');
            // strip components being 0
            $duration = preg_replace('`(?<!\d)0[YMDHMS]`', '', $duration);
            // strip time indicator when no time deltas follow
            $duration = rtrim($duration, 'TP/');

            // ignore time components and replace trivial intervals by their simplest representation
            $isoInterval = str_replace(
                ['-01-01/P1Y', '-01/P1M', '/P1D'],
                '',
                substr($this->after, 0, 10) . $duration
            );
            $before->modify('-1 day');
            if($duration == "/P1Y1D") {
            	return substr($this->after, 0, 10)."/".substr($this->before, 0, 10);
            }
	    

            // suppress start/duration format in favor of start/end
            return preg_replace('`/P.*$`', '/' . substr($this->before, 0, 10), $isoInterval);
        }

        /**
         * @return string
         */
        public function getAfterDate()
        {
            return substr(parent::getAfter(), 0, 10);
        }

        /**
         * @return string
         */
        public function getBeforeDate()
        {
            return substr(parent::getBefore(), 0, 10);
        }
    }
