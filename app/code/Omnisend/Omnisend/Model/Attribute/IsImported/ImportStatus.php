<?php

namespace Omnisend\Omnisend\Model\Attribute\IsImported;

use Omnisend\Omnisend\Model\RequestService;

class ImportStatus
{
    const IMPORT_SUCCESSFUL = 1;
    const IMPORT_FAILED = 0;

    /**
     * @param $response
     * @param bool $testDuplicates
     * @return int
     */
    public function getImportStatus($response, $testDuplicates = false)
    {
        if ($testDuplicates && $response == null) {
            return self::IMPORT_SUCCESSFUL;
        }

        if ($response === null || in_array($response, RequestService::FAILED_RESPONSE_CODES)) {
            return self::IMPORT_FAILED;
        }

        return self::IMPORT_SUCCESSFUL;
    }
}
