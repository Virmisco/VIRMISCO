<?php
    namespace sednasoft\virmisco;

    use AppendIterator;
    use DirectoryIterator;
    use RuntimeException;
    use SplFileInfo;
    use ZipArchive;

    class FocalPlaneImageFileProcessor
    {
        public $jpg420Options = '-qua 95 -opt -pro -dct int -smooth 0 -sample 2x2';
        public $jpg444Options = '-qua 95,33 -opt -pro -dct int -smooth 0 -sample 1x1';
        public $mp4Options = '-s:v 1440x1080 -b:v 12M -keyint_min 5 -strict -2 -movflags faststart';
        public $oggOptions = '-qscale:v 10 -keyint_min 5 -pix_fmt yuvj444p';
        public $gifOptions = '-s:v 200x150';
        public $outputBaseName = 'focal-series';
        public $frameRate = 15;

        /**
         * @param string $inputDir
         * @param string $tempDir
         * @param string $outputDir
         */
        public function processImageFiles($inputDir, $tempDir, $outputDir)
        {
            $pnmDir = sprintf('%s/%s', $tempDir, $this->randomUuid());
            $jpeg420Dir = sprintf('%s/420', $pnmDir);
            $jpeg444Dir = sprintf('%s/444', $pnmDir);
            $zipDir = sprintf('%s/zip', $pnmDir);
            $mp4Path = sprintf('%s/%s.mp4', $outputDir, $this->outputBaseName);
            $oggPath = sprintf('%s/%s.ogg', $outputDir, $this->outputBaseName);
            $gifPath = sprintf('%s/%s.gif', $outputDir, $this->outputBaseName);
            $zipPath = sprintf('%s/%s.zip', $outputDir, $this->outputBaseName);
            $this->mkdir($pnmDir, 0755, true);
            $this->mkdir($jpeg420Dir, 0755, true);
            $this->mkdir($jpeg444Dir, 0755, true);
            $this->mkdir($zipDir, 0755, true);
            $tiffImages = $this->findAndSortTiffImages($inputDir);
            $count = count($tiffImages);
            foreach ($tiffImages as $index => $tiffPath) {
                $this->convertSingleImageFile($index, $count, $tiffPath, $pnmDir, $jpeg420Dir, $jpeg444Dir, $zipDir);
            }
            $this->convertToVideo($jpeg420Dir, $jpeg444Dir, $zipDir, $mp4Path, $oggPath, $gifPath, $zipPath);
            $this->deleteTemporaryFilesAndDirectories($pnmDir, $jpeg420Dir, $jpeg444Dir, $zipDir);
        }

        /**
         * @param string $pnmPath
         * @param string $jpegPath
         * @param string $options
         * @param string $output
         * @return int
         */
        private function convertPnmToJpeg($pnmPath, $jpegPath, $options, &$output)
        {
            $this->exec(
                sprintf(
                    '/usr/bin/cjpeg %s -outfile %s %s 2>&1',
                    $options,
                    escapeshellarg($jpegPath),
                    escapeshellarg($pnmPath)
                ),
                $output,
                $errorCode
            );

            return $errorCode;
        }

        /**
         * @param int $index
         * @param int $count
         * @param string $tiffPath
         * @param string $pnmDir
         * @param string $jpeg420Dir
         * @param string $jpeg444Dir
         * @param string $zipDir
         */
        private function convertSingleImageFile(
            $index,
            $count,
            $tiffPath,
            $pnmDir,
            $jpeg420Dir,
            $jpeg444Dir,
            $zipDir
        ) {
            $indexFromEnd = $count + $count - $index - 1;
            $output = [];
            $pnmPath = sprintf('%s/%03d.pnm', $pnmDir, $index);
            $jpeg420Path = sprintf('%s/%03d.jpg', $jpeg420Dir, $index);
            $jpeg444Path = sprintf('%s/%03d.jpg', $jpeg444Dir, $index);
            $pnmError = $this->convertTiffToPnm($tiffPath, $pnmPath, $output);
            $jpeg420Error = 0;
            $jpeg444Error = 0;
            if (!$pnmError) {
                $jpeg420Error = $this->convertPnmToJpeg($pnmPath, $jpeg420Path, $this->jpg420Options, $output);
                $jpeg444Error = $this->convertPnmToJpeg($pnmPath, $jpeg444Path, $this->jpg444Options, $output);
                $this->unlink($pnmPath);
                if (!$jpeg420Error) {
                    $this->copy($jpeg420Path, sprintf('%s/%03d.jpg', $jpeg420Dir, $indexFromEnd));
                }
                if (!$jpeg444Error) {
                    $this->copy($jpeg444Path, sprintf('%s/%03d.jpg', $jpeg444Dir, $indexFromEnd));
                    $this->copy($jpeg444Path, sprintf('%s/%03d.jpg', $zipDir, $index));
                }
            }
            if ($pnmError || $jpeg420Error || $jpeg444Error) {
                throw new RuntimeException(implode("\n", $output));
            }
        }

        /**
         * @param string $tiffPath
         * @param string $pnmPath
         * @param string $output
         * @return int
         */
        private function convertTiffToPnm($tiffPath, $pnmPath, &$output)
        {
            $this->exec(
                sprintf('/usr/bin/convert %s %s 2>&1', escapeshellarg($tiffPath), escapeshellarg($pnmPath)),
                $output,
                $errorCode
            );

            return $errorCode;
        }

        /**
         * @param string $jpeg420Dir
         * @param string $jpeg444Dir
         * @param string $zipDir
         * @param string $mp4Path
         * @param string $oggPath
         * @param string $gifPath
         * @param string $zipPath
         * @internal param string $mp4Options
         * @internal param string $oggOptions
         * @internal param string $gifOptions
         */
        private function convertToVideo(
            $jpeg420Dir,
            $jpeg444Dir,
            $zipDir,
            $mp4Path,
            $oggPath,
            $gifPath,
            $zipPath
        ) {
            $output = [];
            $mp4Error = $this->createVideoFromImages(
                $jpeg420Dir,
                $mp4Path,
                $this->frameRate,
                'libx264',
                $this->mp4Options,
                $output
            );
            if ($mp4Error) {
                throw new RuntimeException(implode("\n", $output));
            }
            $output = [];
            $oggError = $this->createVideoFromImages(
                $jpeg444Dir,
                $oggPath,
                $this->frameRate,
                'libtheora',
                $this->oggOptions,
                $output
            );
            if ($oggError) {
                throw new RuntimeException(implode("\n", $output));
            }
            $output = [];
            $gifError = $this->createVideoFromImages(
                $jpeg444Dir,
                $gifPath,
                $this->frameRate,
                'gif',
                $this->gifOptions,
                $output
            );
            if ($gifError) {
                throw new RuntimeException(implode("\n", $output));
            }
            $zipError = $this->createZipArchiveFromImages($zipDir, $zipPath);
            if ($zipError) {
                throw new RuntimeException('Unknown error during ZIP compression: ' . $zipError);
            }
        }

        /**
         * @param string $source
         * @param string $destination
         * @param resource $context
         * @return bool
         */
        private function copy($source, $destination, $context = null)
        {
            printf("cp %s %s\n", escapeshellarg($source), escapeshellarg($destination));

            return true;
//            return copy($source, $destination, $context);
        }

        /**
         * @param string $jpegDir
         * @param string $videoPath
         * @param int $frameRate
         * @param string $videoCodec
         * @param string $options
         * @param string $output
         * @return int
         */
        private function createVideoFromImages($jpegDir, $videoPath, $frameRate, $videoCodec, $options, &$output)
        {
            $this->exec(
                sprintf(
                    '/usr/local/sbin/ffmpeg -framerate %d -i %s -an -sn -c:v %s %s %s 2>&1',
                    $frameRate,
                    escapeshellarg($jpegDir . '/%03d.jpg'),
                    $videoCodec,
                    $options,
                    escapeshellarg($videoPath)
                ),
                $output,
                $errorCode
            );

            return $errorCode;
        }

        /**
         * @param string $jpegDir
         * @param string $zipPath
         * @return int
         */
        private function createZipArchiveFromImages($jpegDir, $zipPath)
        {
            $this->exec(sprintf('pushd %s', escapeshellarg($jpegDir)), $output, $errorCode);
            if (!$errorCode) {
                $this->exec(
                    sprintf('/usr/bin/zip -9 %s *.jpg 2>&1', escapeshellarg($zipPath)),
                    $output,
                    $errorCode
                );
                $this->exec('popd', $output, $errorCode);
            }

            return $errorCode;
//            $zipArchive = new ZipArchive();
//            if (!$zipArchive->open($zipPath, ZipArchive::CREATE)) {
//                return 1;
//            } else {
//                foreach (new DirectoryIterator($jpegDir) as $jpegFileInfo) {
//                    if ($jpegFileInfo->isFile() && $jpegFileInfo->getExtension() == 'jpg') {
//                        if (!$zipArchive->addFile($jpegFileInfo->getRealPath(), $jpegFileInfo->getBasename())) {
//                            return 2;
//                        }
//                    }
//                }
//
//                return $zipArchive->close() ? 0 : 3;
//            }
        }

        /**
         * @param string $pnmDir
         * @param string $jpeg420Dir
         * @param string $jpeg444Dir
         * @param string $zipDir
         */
        private function deleteTemporaryFilesAndDirectories($pnmDir, $jpeg420Dir, $jpeg444Dir, $zipDir)
        {
//            $appendIterator = new AppendIterator();
//            $appendIterator->append(new DirectoryIterator($jpeg420Dir));
//            $appendIterator->append(new DirectoryIterator($jpeg444Dir));
//            $appendIterator->append(new DirectoryIterator($zipDir));
//            foreach ($appendIterator as $fileInfo) {
//                if ($fileInfo->isFile() && $fileInfo->getExtension() == 'jpg') {
//                    $this->unlink($fileInfo->getRealPath());
//                }
//            }
            $this->rmdir($jpeg420Dir);
            $this->rmdir($jpeg444Dir);
            $this->rmdir($zipDir);
            $this->rmdir($pnmDir);
        }

        /**
         * @param string $command
         * @param array $output
         * @param int $errorCode
         * @return string
         */
        private function exec($command, &$output = null, &$errorCode = null)
        {
            echo $command, "\n";

            return true;
//            return exec($command, $output, $errorCode);
        }

        /**
         * @param string $inputDir
         * @return array
         */
        private function findAndSortTiffImages($inputDir)
        {
            $tiffImages = [];
            /** @var SplFileInfo $fileInfo */
            foreach (new DirectoryIterator($inputDir) as $fileInfo) {
                if ($fileInfo->isFile() && strtolower($fileInfo->getExtension()) === 'tif') {
                    $tiffImages[preg_replace('<\\D+>', '', $fileInfo->getBasename()) - 0] = $fileInfo->getRealPath();
                }
            }
            ksort($tiffImages, SORT_NUMERIC);
            $tiffImages = array_values($tiffImages);

            return $tiffImages;
        }

        /**
         * @param string $pathname
         * @param int $mode
         * @param bool $recursive
         * @param resource $context
         * @return bool
         */
        private function mkdir($pathname, $mode, $recursive = false, $context = null)
        {
            printf("mkdir -m %04o %s %s\n", $mode, $recursive ? '-p' : '', escapeshellarg($pathname));

            return true;
//            return mkdir($pathname, $mode, $recursive, $context);
        }

        /**
         * @return string
         */
        private function randomUuid()
        {
            // http://php.net/manual/en/function.uniqid.php#94959
            return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                // 32 bits for "time_low"
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                // 16 bits for "time_mid"
                mt_rand(0, 0xffff),
                // 16 bits for "time_hi_and_version",
                // four most significant bits holds version number 4
                mt_rand(0, 0x0fff) | 0x4000,
                // 16 bits, 8 bits for "clk_seq_hi_res",
                // 8 bits for "clk_seq_low",
                // two most significant bits holds zero and one for variant DCE1.1
                mt_rand(0, 0x3fff) | 0x8000,
                // 48 bits for "node"
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );
        }

        /**
         * @param string $path
         * @param resource $context
         * @return bool
         */
        private function rmdir($path, $context = null)
        {
            printf("rm -r '%s'\n", $path);

            return true;
//            return rmdir($path, $context);
        }

        /**
         * @param string $path
         * @param resource $context
         * @return bool
         */
        private function unlink($path, $context = null)
        {
            printf("rm '%s'\n", $path);

            return true;
//            return unlink($path, $context);
        }
    }
