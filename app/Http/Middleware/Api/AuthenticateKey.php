<?php

namespace App\Http\Middleware\Api;

use Closure;
use App\Models\ApiKey;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Encryption\Encrypter;
use App\Exceptions\Repository\RecordNotFoundException;
use App\Contracts\Repository\ApiKeyRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AuthenticateKey
{
    /**
     * @var \Illuminate\Auth\AuthManager
     */
    private $auth;

    /**
     * @var \Illuminate\Contracts\Encryption\Encrypter
     */
    private $encrypter;

    /**
     * @var \App\Contracts\Repository\ApiKeyRepositoryInterface
     */
    private $repository;

    /**
     * AuthenticateKey constructor.
     *
     * @param \App\Contracts\Repository\ApiKeyRepositoryInterface $repository
     * @param \Illuminate\Auth\AuthManager                                $auth
     * @param \Illuminate\Contracts\Encryption\Encrypter                  $encrypter
     */
    public function __construct(ApiKeyRepositoryInterface $repository, AuthManager $auth, Encrypter $encrypter)
    {
        $this->auth = $auth;
        $this->encrypter = $encrypter;
        $this->repository = $repository;
    }

    /**
     * Handle an API request by verifying that the provided API key
     * is in a valid format and exists in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param int                      $keyType
     * @return mixed
     *
     * @throws \App\Exceptions\Model\DataValidationException
     * @throws \App\Exceptions\Repository\RecordNotFoundException
     */
    public function handle(Request $request, Closure $next, int $keyType)
    {
        if (is_null($request->bearerToken())) {
            throw new HttpException(401, null, null, ['WWW-Authenticate' => 'Bearer']);
        }

        $raw = $request->bearerToken();
        $identifier = substr($raw, 0, ApiKey::IDENTIFIER_LENGTH);
        $token = substr($raw, ApiKey::IDENTIFIER_LENGTH);

        try {
            $model = $this->repository->findFirstWhere([
                ['identifier', '=', $identifier],
                ['key_type', '=', $keyType],
            ]);
        } catch (RecordNotFoundException $exception) {
            throw new AccessDeniedHttpException;
        }

        if (! hash_equals($this->encrypter->decrypt($model->token), $token)) {
            throw new AccessDeniedHttpException;
        }

        $this->auth->guard()->loginUsingId($model->user_id);
        $request->attributes->set('api_key', $model);
        $this->repository->withoutFreshModel()->update($model->id, ['last_used_at' => CarbonImmutable::now()]);

        return $next($request);
    }
}
