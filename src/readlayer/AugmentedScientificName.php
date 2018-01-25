<?php
    namespace sednasoft\virmisco\readlayer;

    use sednasoft\virmisco\readlayer\entity\ScientificName;

    class AugmentedScientificName extends ScientificName
    {
        const F_BINOMIAL = 'G S';
        const F_BINOMIAL_AUTHORSHIP = 'G S (A, Y)';
        const F_FULL_NAME = 'G [g] S s (A, Y)';
        const F_SUBRANKS = 'G [g] S s';

        /**
         * @return string
         */
        public function __toString()
        {
            return $this->format();
        }

        /**
         * @param string $format One of the AugmentedScientificName::F_* constants or combinations of the following
         * characters (spaces will be normalized after substitution):
         *
         * G: the genus name
         *
         * [: a left paranthesis when the subgenus name is non-empty
         *
         * g: the subgenus name
         *
         * ]: a right paranthesis when the subgenus name is non-empty
         *
         * S: the species epithet
         *
         * s: the infraspecific epithet
         *
         * (: a left paranthesis when the authorship must be parenthesized and an author or a year is present
         *
         * A: the author(s)
         *
         * ,: a comma when both author and year are present
         *
         * Y: the year
         *
         * ): a right paranthesis when the authorship must be parenthesized and an author or a year is present
         *
         * @return string
         */
        public function format($format = self::F_FULL_NAME)
        {
            $result = '';
            foreach (str_split($format) as $char) {
                switch ($char) {
                    case ' ':
                        $result .= ' ';
                        break;
                    case 'G':
                        $result .= $this->getGenus();
                        break;
                    case '[':
                        $result .= $this->getSubgenus() ? '(' : '';
                        break;
                    case 'g':
                        $result .= $this->getSubgenus();
                        break;
                    case ']':
                        $result .= $this->getSubgenus() ? ')' : '';
                        break;
                    case 'S':
                        $result .= $this->getSpecificEpithet();
                        break;
                    case 's':
                        $result .= $this->getInfraspecificEpithet();
                        break;
                    case '(':
                        $result .= $this->getIsParenthesized() && ($this->getAuthorship() || $this->getYear())
                            ? '('
                            : '';
                        break;
                    case 'A':
                        $result .= $this->getAuthorship();
                        break;
                    case ',':
                        $result .= $this->getAuthorship() && $this->getYear() ? ',' : '';
                        break;
                    case 'Y':
                        $result .= $this->getYear();
                        break;
                    case ')':
                        $result .= $this->getIsParenthesized() && ($this->getAuthorship() || $this->getYear())
                            ? ')'
                            : '';
                        break;
                }
            }

            return preg_replace('<\s+>', ' ', trim($result));
        }
    }
