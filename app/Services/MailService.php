<?php

namespace App\Services;

use App\Contracts\MailServiceInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\WalletRepositoryInterface;
use App\Mail\TransactionNotificationMail;
use Illuminate\Support\Facades\Mail;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class MailService implements MailServiceInterface
{

    public function __construct(
        private WalletRepositoryInterface $walletRepository,
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function send(array $transactionInfo): void
    {
        $walletId = $transactionInfo['wallet_id'];
        $userWallet = $this->walletRepository->findOneBy($walletId);
        $user = $this->userRepository->findOneBy($userWallet['user_id']);
        $status = $transactionInfo['status'];
        $message = $transactionInfo['message'];

        $mailMessage = (new TransactionNotificationMail($user['full_name'], $message, $status));

        Mail::to($user['email'])->send($mailMessage);
    }
}
