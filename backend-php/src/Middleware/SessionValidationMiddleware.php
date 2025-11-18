<?php

namespace App\Middleware;

use App\Factory\ContainerFactory;
use App\Contract\ServerRequestContract;
use App\Domain\UserSession\Service\ValidateSession\ValidateSessionService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Exception\ValidationException;
use App\Util\Utilities;
use Exception;
use PDO;

final class SessionValidationMiddleware
{
    /**
     * @throws ValidationException
     */
    public function __invoke(ServerRequestContract $requestHandler): ServerRequestContract
    {
        try {
            $authorizationHeader = $requestHandler->getHeaderLine('authorization');
            if (strlen($authorizationHeader) > 0) {
                $authorization = (new Utilities)->sanitizeString($authorizationHeader);
                if (count(explode(' ', $authorization)) > 0) {
                    list($authType, $authToken) = explode(' ', $authorization);

                    $tokenBits = str_split($authToken, 32);
                    if ($authType === 'Bearer' || count($tokenBits) === 2) {
                        $container = (new ContainerFactory())->createInstance();
                        $validateSessionService = new ValidateSessionService($container->get(PDO::class));

                        $sessionData = $validateSessionService->validateSession([
                            'sessionId' => $tokenBits[1],
                            'sessionKey' => $tokenBits[0]
                        ]);
                        $requestHandler->setAttributes($sessionData, 'sessionData');
                        return $requestHandler;
                    }
                }
            }
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface|Exception $exception) {
            throw new ValidationException($exception->getMessage(), $exception->getCode());
        }

        throw new ValidationException('Invalid or expired session, please login again.', 401);
    }
}
