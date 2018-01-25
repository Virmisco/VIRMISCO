<?php
    namespace sednasoft\virmisco\util;

    class CustomLasDecorator extends AbstractTechnicalDatasetDecorator
    {
        /**
         * @param int $imageIndex
         * @return int
         */
        public function getCameraActivePixelsHor($imageIndex)
        {
            return null;
            //return 2048;
        }

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getCameraActivePixelsVer($imageIndex)
        {
            return null;
            //return 1536;
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraAdcResolution($imageIndex)
        {
            return null;
            //return '10-bit, on-chip';
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getCameraChipHeight($imageIndex)
        {
            return null;
            //return 4.92e-3;
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getCameraChipWidth($imageIndex)
        {
            return null;
            //return 6.55e-3;
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraColorFilterArray($imageIndex)
        {
            return null;
            //return 'RGB Bayer pattern';
        }

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getCameraDynamicRange($imageIndex)
        {
            return null;
            //return 61;
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraMaker($imageIndex)
        {
            return null;
            //return 'Leica';
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraOpticalFormat($imageIndex)
        {
            return null;
            //return '1/2-inch (4:3)';
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getCameraPixelHeight($imageIndex)
        {
            return null;
            //return 3.2e-6;
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getCameraPixelWidth($imageIndex)
        {
            return null;
            //return 3.2e-6;
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraProtectiveColorFilter($imageIndex)
        {
            return null;
            //return 'Hoya CM500S (IR cut-coating filter at 650 nm)';
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraReadoutNoise($imageIndex)
        {
            return null;
            //return '1.8 LSB (10 bit) typical';
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraSensorArticleOrSerialNumber($imageIndex)
        {
            return null;
            //return 'MT9T001P12STC';
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraSensorMaker($imageIndex)
        {
            return null;
            //return 'Aptina';
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraSensorName($imageIndex)
        {
            return null;
            //return 'MT9T001';
        }

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getCameraSnrMax($imageIndex)
        {
            return null;
            //return 43;
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCameraMountAdapterArticleOrSerialNumber($imageIndex)
        {
            return null;
            //return '11541544';
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getMicroscopeCameraMountAdapterMagnification($imageIndex)
        {
            return null;
            //return 0.55;
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCameraMountAdapterMaker($imageIndex)
        {
            return null;
            //return 'Leica';
        }

        /**
         * @param int $imageIndex
         * @return null
         */
        public function getMicroscopeCameraMountAdapterName($imageIndex)
        {
            return null;
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCondenserMaker($imageIndex)
        {
            return null;
            //return 'Leica';
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCondenserTurretPrismMaker($imageIndex)
        {
            return null;
            //return 'Leica';
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeDicTurretPrismMaker($imageIndex)
        {
            return null;
            //return 'Leica';
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeMagnificationChangerMaker($imageIndex)
        {
            return null;
            //return 'Leica';
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeNosepieceObjectiveMaker($imageIndex)
        {
            return null;
            //return 'Leica';
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopePortsMaker($imageIndex)
        {
            return null;
            //return 'Leica';
        }

        /**
         * @param int $imageIndex
         * @return null
         */
        public function getMicroscopeSettingsDicPrismPosition($imageIndex)
        {
            return null;
            //return null;
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeStandMaker($imageIndex)
        {
            return null;
            //return 'Leica';
        }
    }
