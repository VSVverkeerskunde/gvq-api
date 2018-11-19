<?php

namespace VSV\GVQ_API\Common;

use Symfony\Component\HttpFoundation\Response;

class CsvResponse extends Response
{
    /**
     * @var bool
     */
    protected $streamed;

    /**
     * @var bool
     */
    private $headersSent;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var \VSV\GVQ_API\Common\CsvData
     */
    private $csvData;

    public function __construct(
        string $filename,
        CsvData $csvData
    ) {
        parent::__construct(NULL, 200);

        $this->csvData = $csvData;

        $this->filename = $filename;

        $this->headersSent = false;
        $this->streamed = false;

        $debug = false;

        if (!$debug) {
            $this->headers->set('Content-Encoding', 'UTF-16LE');
            $this->headers->set('Content-Type',
                'application/csv; charset=UTF-16LE');
            $this->headers->set('Content-Transfer-Encoding', 'binary');
            $this->headers->set(
                'Content-Disposition',
                $this->headers->makeDisposition('attachment', $this->filename)
            );
        }
    }

    /**
     * {@inheritdoc}
     *
     * This method only sends the headers once.
     *
     * @return $this
     */
    public function sendHeaders()
    {
        if ($this->headersSent) {
            return $this;
        }

        $this->headersSent = true;

        return parent::sendHeaders();
    }

    /**
     * {@inheritdoc}
     *
     * This method only sends the content once.
     *
     * @return $this
     */
    public function sendContent()
    {
        if ($this->streamed) {
            return $this;
        }

        $this->streamed = true;

        $f = fopen('php://output', 'r+');

        $this->writeBOM($f);
        $this->writeSeparatorHintLine($f);

        foreach ($this->csvData->rows() as $row) {
            $this->writeCells($f, $row);
        }

        fclose($f);

        return $this;
    }

    private function writeBOM($f)
    {
        fwrite($f, chr(0xFF).chr(0xFE));
    }

    private function writeSeparatorHintLine($f)
    {
        fwrite($f, $this->convertEncoding('sep=,' . PHP_EOL));
    }

    private function writeCells($f, $cells)
    {
        $cells = array_map(
            [$this, 'convertEncoding'],
            $cells
        );

        fputcsv($f, $cells, ',', '"', '\\');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LogicException when the content is not null
     *
     * @return $this
     */
    public function setContent($content)
    {
        if (null !== $content) {
            throw new \LogicException('The content cannot be set on a StreamedResponse instance.');
        }

        $this->streamed = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return false
     */
    public function getContent()
    {
        return false;
    }

    /**
     * @param string $string
     * @return string
     */
    private function convertEncoding(string $string): string
    {
        return mb_convert_encoding($string, 'UTF-16LE', 'UTF-8');
    }
}
