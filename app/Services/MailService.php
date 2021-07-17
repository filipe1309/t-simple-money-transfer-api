<?php

namespace App\Services;

use App\Mail\TransactionNotificationMail;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use Illuminate\Support\Facades\Mail;

class MailService
{

    public function __construct(
        private WalletRepository $walletRepository,
        private UserRepository $userRepository
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
