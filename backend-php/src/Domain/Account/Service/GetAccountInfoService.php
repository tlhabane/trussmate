<?php

namespace App\Domain\Account\Service;

use App\Domain\Account\Repository\GetAccountRepository;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Util\PathConverter;
use App\Util\Utilities;
use Exception;
use PDO;

final class GetAccountInfoService
{
    private GetAccountRepository $getAccountRepository;

    public function __construct(PDO $connection)
    {
        $this->getAccountRepository = new GetAccountRepository($connection);
    }

    public function getAccount(string $account_no): array
    {
        $sanitizedAccountNo = Utilities::sanitizeString($account_no);
        $records = [];
        $accounts = $this->getAccountRepository->getAccount($sanitizedAccountNo);
        foreach ($accounts as $account) {
            $logo = '';
            if (!empty($account['logo'])) {
                $pathFinder = new PathConverter();
                $logo = $pathFinder->getFileUrl($account['logo']);
            }

            $records[] = [
                'logo' => $logo,
                'registrationNo' => $account['registration_no'],
                'registeredName' => Utilities::decodeUTF8($account['registered_name']),
                'vatNo' => $account['vat_no'],
                'tradingName' => Utilities::decodeUTF8($account['trading_name']),
                'tel' => $account['tel'],
                'altTel' => $account['alt_tel'],
                'email' => $account['email'],
                'web' => $account['web'],
                'address' => Utilities::decodeUTF8($account['address'])
            ];
        }

        return ['records' => $records];
    }
}
