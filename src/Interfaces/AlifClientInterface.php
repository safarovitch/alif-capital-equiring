<?php

namespace RasulSafarovitch\AlifPaymentIntegration\Interfaces;

interface AlifClientInterface
{
    public function createInvoice(array $data): array;
    public function checkStatus(int $invoiceId): array;
    public function cancelInvoice(int $invoiceId): array;
}
