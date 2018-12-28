<?php

namespace HieroTeX\Hieroglyph;

class Inscription {
    private $contentMdc;

    public function __construct(string $contentMdc) {
        $this -> contentMdc = $contentMdc;
    }

    public function toSvg() : string {
        try {
            $templatePath = __DIR__ . "/resources/template.htx";
            // Get templated document
            $hieroContent = $this->latexSanitise($this->contentMdc);
            $templated = str_replace("__HIERO_CONTENT__", $hieroContent, file_get_contents($templatePath));
            // Feed through sesh
            $texFilename = "input.tex";
            $sesh = "sesh";
            $tmpf = tempnam(sys_get_temp_dir(), "hieroglyph-");
            $dir = $tmpf . ".d";
            @mkdir($dir);
            $descriptors = array(
                0 => array("pipe", "r"),
                1 => array("file", $dir . "/" . $texFilename, "w"),
                2 => array("pipe", "w")
            );
            $process = proc_open($sesh, $descriptors, $pipes, $dir);
            if ($process === false) {
                throw new \Exception("Failed to start $sesh.");
            }
            fwrite($pipes[0], $templated);
            fclose($pipes[0]);

            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            $return_value = proc_close($process);
            if ($return_value !== 0) {
                throw new \Exception("$sesh returned $return_value. stderr was: " . $stderr);
            }
            // Feed through pdflatex
            $latex = "pdflatex --interaction=nonstopmode " . escapeshellarg($texFilename);
            $descriptors = array(
                0 => array("pipe", "r"),
                1 => array("pipe", "w"),
                2 => array("pipe", "w")
            );
            $process = proc_open($latex, $descriptors, $pipes, $dir);
            if ($process === false) {
                throw new \Exception("Failed to start $latex.");
            }
            fclose($pipes[0]);
            $stdout = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            $return_value = proc_close($process);
            if ($return_value !== 0) {
                // Depending on the type of error, either stderr or stdout is relevant.
                if (trim($stderr) == "") {
                    throw new \Exception("$latex returned $return_value. stdout was: " . $stdout);
                } else {
                    throw new \Exception("$latex returned $return_value. stderr was: " . $stderr);
                }

            }
            $pdfFilename = "input.pdf";
            if (!file_exists($dir . "/" . $pdfFilename)) {
                throw new \Exception("$pdfFilename was not created by $latex");
            }
            // Feed through pdf2svg
            $svgFilename = "input.svg";
            $pdftosvg = "pdf2svg " . escapeshellarg($dir . "/" . $pdfFilename) . " " . escapeshellarg($svgFilename);
            $descriptors = array(
                0 => array("pipe", "r"),
                1 => array("pipe", "w"),
                2 => array("pipe", "w")
            );
            $process = proc_open($pdftosvg, $descriptors, $pipes, $dir);
            if ($process === false) {
                throw new \Exception("Failed to start $pdftosvg.");
            }
            fclose($pipes[0]);
            // TODO not used, can we discard somehow?
            $stdout = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            $return_value = proc_close($process);
            if ($return_value !== 0) {
                // Depending on the type of error, either stderr or stdout is relevant.
                throw new \Exception("$pdftosvg returned $return_value. stderr was: " . $stderr);
            }
            if (!file_exists($dir . "/" . $svgFilename)) {
                throw new \Exception("$svgFilename was not created by $pdftosvg");
            }
            $contents = file_get_contents($dir . "/" . $svgFilename);
        } finally {
            // Always clean up temp files
            @unlink($dir . "/" . "input.aux");
            @unlink($dir . "/" . "input.dic");
            @unlink($dir . "/" . "input.log");
            @unlink($dir . "/" . $texFilename);
            @unlink($dir . "/" . $pdfFilename);
            @unlink($dir . "/" . $svgFilename);
            @rmdir($dir);
        }
        return $contents;
    }

    public function latexSanitise(string $inp) : string {
        // Reject or escape input characters as necessary
        if(preg_match("/^[0-9A-Zabcdfghiklmnpqrstvwxyz_*!: -]*$/", $inp) === false) {
            throw new \Exception("Input is not valid");
        }
        return str_replace("!", "\!", $inp);
    }
}