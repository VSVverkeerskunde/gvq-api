<?php

namespace VSV\GVQ_API\Common\Controllers;

use Symfony\Component\HttpFoundation\Response;

class ResponseFactory
{
    /**
     * @param string $data
     * @param string $model
     * @return Response
     */
    public function createCsvResponse(string $data, string $model): Response
    {
        $response = new Response(
            $this->createCsvData($data)
        );

        $response->headers->set('Content-Encoding', 'UTF-8');
        $response->headers->set('Content-Type', 'application/csv; charset=UTF-8');
        $response->headers->set('Content-Transfer-Encoding', 'binary');

        $now = new \DateTime();
        $fileName = $model.'_'.$now->format(\DateTime::ATOM).'.csv';
        $response->headers->set(
            'Content-Disposition',
            'attachment; filename="'.$fileName.'"'
        );

        return $response;
    }

    /**
     * @param string $data
     * @return string
     */
    private function createCsvData(string $data): string
    {
        /**
         * @see: https://github.com/thephpleague/csv/blob/507815707cbdbebaf076873bff04cd6ad65fe0fe/docs/9.0/connections/bom.md
         */
        $csvData = chr(0xFF).chr(0xFE);
        $csvData .= mb_convert_encoding('sep=,'.PHP_EOL.$data, 'UTF-16LE', 'UTF-8');
        return $csvData;
    }
}
