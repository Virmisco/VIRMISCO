<?php
    namespace sednasoft\virmisco\util;

    class ImageFileConverter
    {
        private $destinationFiles = [];
        private $sourceFiles = [];

        public function execute()
        {
            foreach ($this->sourceFiles as $index => $path) {
                $path = realpath($path);
                $dir = dirname($path);
                $tempFile = sprintf('%s/%04d.tmp.pnm', $dir, $index);
                $jpg444File = sprintf('%s/%04d.444.jpg', $dir, $index);
                $jpg420File = sprintf('%s/%04d.420.jpg', $dir, $index);
                exec(
                    $output[] = sprintf('/usr/bin/convert %s %s', escapeshellarg($path), escapeshellarg($tempFile)),
                    $output,
                    $errorLevel
                );
                $output[] = 'ERRORLEVEL=' . $errorLevel;
                // for IE
                exec(
                    $output[] = sprintf(
                        'cjpeg -qua 95 -opt -pro -dct int -smooth 0 -sample 2x2 -outfile %s %s',
                        escapeshellarg($jpg420File),
                        escapeshellarg($tempFile)
                    ),
                    $output,
                    $errorLevel
                );
                $output[] = 'ERRORLEVEL=' . $errorLevel;
                // for real browsers
                exec(
                    $output[] = sprintf(
                        'cjpeg -qua 95,33 -opt -pro -dct int -smooth 0 -sample 1x1 -outfile %s %s',
                        escapeshellarg($jpg444File),
                        escapeshellarg($tempFile)
                    ),
                    $output,
                    $errorLevel
                );
                $output[] = 'ERRORLEVEL=' . $errorLevel;
                if (is_file($tempFile)) {
                    unlink($tempFile);
                }
            }
            file_put_contents(__FILE__ . '.log', implode("\n", $output));
        }

        /**
         * @return string[] An arrays of destination file names, where the key corresponds to the focal plane image.
         */
        public function getDestinationFiles()
        {
            return $this->destinationFiles;
        }

        /**
         * @param string[] $tempFilePaths
         */
        public function registerSourceFiles(array $tempFilePaths)
        {
            $this->sourceFiles = $tempFilePaths;
        }
    }
