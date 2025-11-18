<?php

namespace App\Util;

final class DataPagination
{
    public static function getPagingLinks(int $pagingPage, int $totalRows, int $recordsPerPage): array
    {
        // paging array
        $pagingArray = [];

        $pagingArray['totalRecords'] = $totalRows;

        // calculate total pages
        $pagingArray['totalPages'] = ceil($totalRows / $recordsPerPage);

        // current page
        $pagingArray['currentPage'] = $pagingPage;

        // other GET request params
        $pageUrl = self::getPagingUrl();

        // button for first page
        $pagingArray['first'] = $pagingPage > 1 ? $pageUrl . 'page=1' : '';

        // range of links to show
        $range      = 4;

        // display links to 'range of pages' around 'current page'
        $initial_num = $pagingPage - $range;
        $condition_limit_num = ($pagingPage + $range) + 1;

        $pagingArray['pages'] = [];
        $pageCount = 0;

        for ($x = $initial_num; $x < $condition_limit_num; $x++) {
            // be sure '$x is greater than 0' AND 'less than or equal to the $total_pages'
            if (($x > 0) && ($x <= $pagingArray['totalPages'])) {
                $pagingArray['pages'][$pageCount] = [
                    'page' => $x,
                    'url' => $pageUrl . 'page=' . $x,
                    'currentPage'  => $x === $pagingPage
                ];

                $pageCount++;
            }
        }

        // button for last page
        $pagingArray['last'] = $pagingPage < $pagingArray['totalPages']
            ? $pageUrl . 'page=' . $pagingArray['totalPages'] : '';

        return $pagingArray;
    }

    private static function getPagingUrl(): string
    {
        if (!isset($_SERVER['REQUEST_URI']) || !filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL)) {
            return '';
        }

        $currentUri = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
        $currentUriParams = explode('?', $currentUri);
        $paramsArray = explode('&', end($currentUriParams));

        $finalUrlParams = '';
        foreach ($paramsArray as $index => $urlParam) {
            if (!str_starts_with(strtolower($urlParam), 'page=')) {
                $finalUrlParams .= $index === 0 ? $urlParam : '&' . $urlParam;
            }
        }

        return $currentUriParams[0] . '?' . $finalUrlParams . '&';
    }

    /**
     * Pagination helper function to calculate number of records to offset based on page no & records per page
     *
     * @param int $pageNo
     * @param int $recordsPerPage
     * @return array
     */
    public static function getRecordOffset(int $pageNo, int $recordsPerPage = 30): array
    {
        if ($pageNo > 0) {
            $recordStart = ($recordsPerPage * $pageNo) - $recordsPerPage;
            return ['recordStart' => $recordStart, 'recordsPerPage' => $recordsPerPage];
        }
        return ['recordStart' => 0, 'recordsPerPage' => 0];
    }
}
